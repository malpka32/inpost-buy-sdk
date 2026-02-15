<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Auth;

use malpka32\InPostBuySdk\Config\InPostBuyEndpoints;
use malpka32\InPostBuySdk\Exception\ApiException;
use malpka32\InPostBuySdk\Exception\ApiExceptionFactory;
use malpka32\InPostBuySdk\Mapper\ArrayHelper;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * OAuth2 Authorization Code flow with PKCE (RFC 7636).
 * Generates code_verifier/code_challenge, initiates authorization, exchanges code for tokens.
 */
final class PkceOAuth2Client
{
    private const CODE_VERIFIER_LENGTH = 64;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Initiates PKCE flow. Generates state, code_verifier, code_challenge and returns URL to redirect user.
     *
     * @param string         $redirectUri         Callback URL after user authorizes
     * @param string         $clientId            OAuth2 client ID
     * @param bool           $sandbox             Use sandbox environment
     * @param string|null    $authorizeUrlOverride Override authorize URL (optional)
     * @param string|null    $scopes              Space-separated scopes (default: SCOPES_DEFAULT)
     * @return array{authorize_url: string, state: string}
     */
    public function initiateAuthorization(
        string $redirectUri,
        string $clientId,
        bool $sandbox,
        PkceStateStorageInterface $stateStorage,
        ?string $authorizeUrlOverride = null,
        ?string $scopes = null,
    ): array {
        $state = $this->generateRandomString(32);
        $codeVerifier = $this->generateRandomString(self::CODE_VERIFIER_LENGTH);
        $codeChallenge = $this->computeCodeChallenge($codeVerifier);

        $stateStorage->setState($state, $codeVerifier);

        $authorizeUrl = InPostBuyEndpoints::authorizeUrl($sandbox, $authorizeUrlOverride);
        $scopeString = $scopes ?? implode(' ', InPostBuyEndpoints::getAvailableScopes());

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopeString,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return [
            'authorize_url' => $authorizeUrl . (str_contains($authorizeUrl, '?') ? '&' : '?') . $params,
            'state' => $state,
        ];
    }

    /**
     * Exchanges authorization code for access_token and refresh_token.
     *
     * @return array{access_token: string, refresh_token: string, expires_in: int}
     */
    public function exchangeCodeForTokens(
        string $code,
        string $redirectUri,
        string $clientId,
        string $clientSecret,
        string $state,
        string $tokenUrl,
        PkceStateStorageInterface $stateStorage,
        TokenStorageInterface $tokenStorage,
    ): array {
        $storedState = $stateStorage->getState();
        if ($storedState === null || !hash_equals($storedState, $state)) {
            throw new ApiException('Invalid or missing PKCE state – possible CSRF or session mismatch');
        }

        $codeVerifier = $stateStorage->getCodeVerifier();
        if ($codeVerifier === null) {
            throw new ApiException('Missing code_verifier in PKCE state storage');
        }

        $response = $this->httpClient->request('POST', $tokenUrl, [
            'body' => http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code_verifier' => $codeVerifier,
            ]),
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ]);

        if ($response->getStatusCode() >= 400) {
            $stateStorage->deleteState();
            throw ApiExceptionFactory::fromResponse($response, 'OAuth2 token exchange failed');
        }

        $stateStorage->deleteState();

        $data = json_decode($response->getContent(false), true);
        if (!is_array($data)) {
            throw new ApiException('Invalid OAuth2 token response');
        }

        $accessToken = ArrayHelper::asString($data['access_token'] ?? '');
        $refreshToken = ArrayHelper::asString($data['refresh_token'] ?? '');
        $expiresIn = ArrayHelper::asInt($data['expires_in'] ?? 0);

        if ($accessToken === '' || $refreshToken === '') {
            throw new ApiException('Missing access_token or refresh_token in OAuth2 response');
        }

        $expiresAt = time() + $expiresIn;
        $tokenStorage->setTokens($accessToken, $refreshToken, $expiresAt);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
        ];
    }

    /**
     * Refreshes access_token using refresh_token.
     *
     * @return string New access token (also persisted via tokenStorage)
     */
    public function refreshAccessToken(
        string $refreshToken,
        string $clientId,
        string $clientSecret,
        string $tokenUrl,
        TokenStorageInterface $tokenStorage,
    ): string {
        $response = $this->httpClient->request('POST', $tokenUrl, [
            'body' => http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]),
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ]);

        if ($response->getStatusCode() >= 400) {
            throw ApiExceptionFactory::fromResponse($response, 'OAuth2 token refresh failed');
        }

        $data = json_decode($response->getContent(false), true);
        if (!is_array($data)) {
            throw new ApiException('Invalid OAuth2 refresh response');
        }

        $accessToken = ArrayHelper::asString($data['access_token'] ?? '');
        $newRefreshToken = ArrayHelper::asString($data['refresh_token'] ?? $refreshToken);
        $expiresIn = ArrayHelper::asInt($data['expires_in'] ?? 3600);

        if ($accessToken === '') {
            throw new ApiException('Missing access_token in OAuth2 refresh response');
        }

        $effectiveRefreshToken = $newRefreshToken !== '' ? $newRefreshToken : $refreshToken;
        if ($effectiveRefreshToken === '') {
            throw new ApiException('No refresh token in OAuth2 refresh response');
        }

        $expiresAt = time() + $expiresIn;
        $tokenStorage->setTokens($accessToken, $effectiveRefreshToken, $expiresAt);

        return $accessToken;
    }

    private function generateRandomString(int $length): string
    {
        $byteLength = max(1, (int) ceil($length * 3 / 4));
        $bytes = random_bytes($byteLength);
        $base64 = base64_encode($bytes);

        return substr(strtr($base64, '+/', '-_'), 0, $length);
    }

    private function computeCodeChallenge(string $codeVerifier): string
    {
        $hash = hash('sha256', $codeVerifier, true);

        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }
}
