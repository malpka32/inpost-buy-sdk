<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Auth;

/**
 * Storage for PKCE flow: state and code_verifier.
 * Consumer (e.g. PrestaShop module) provides implementation backed by its persistence.
 */
interface PkceStateStorageInterface
{
    /**
     * Persists state and code_verifier for later verification during token exchange.
     */
    public function setState(string $state, string $codeVerifier): void;

    /**
     * Returns stored state or null if not found.
     */
    public function getState(): ?string;

    /**
     * Returns stored code_verifier or null if not found.
     */
    public function getCodeVerifier(): ?string;

    /**
     * Removes state and code_verifier after token exchange (or on error).
     */
    public function deleteState(): void;
}
