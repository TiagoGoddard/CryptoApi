<?php namespace TiagoGoddard\Cryptoapi\KeyGenerators;

interface KeyGeneratorInterface {
    public function generateKeyPair($keyPath, $keySize);
}
