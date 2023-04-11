<?php

namespace Support\Router\ClassParser;

class ClassParser
{
    private const TOKEN_ARRAY_ID_CELL = 0;
    /**
     * @var int[]
     */
    private const IGNORE_TOKENS = [
        T_WHITESPACE
    ];

    /**
     * @var Token[]
     */
    private array $tokens = [];

    public function __construct(string $fileContents)
    {
        $this->getTokens($fileContents);
    }

    private function getTokens(string $fileContents) : void
    {
        $rawTokens = token_get_all($fileContents);

        foreach ($rawTokens as $rawToken) {
            if (! is_array($rawToken) || in_array($rawToken[self::TOKEN_ARRAY_ID_CELL], self::IGNORE_TOKENS)) {
                continue;
            }

            $this->tokens[] = new Token(...$rawToken);
        }
    }

    public function getNamespace() : ?string
    {
        $nsOffset = $this->getNextTokenOffset(T_NAMESPACE);

        if ($nsOffset === null) {
            return null;
        }

        return $this->getNextToken(T_NAME_QUALIFIED, $nsOffset)->token;
    }

    /**
     * @return string[]
     */
    public function getClasses() : array
    {
        $ns = $this->getNamespace();
        $classes = [];
        $currentClassOffset = 0;

        while (($currentClassOffset = $this->getNextTokenOffset(T_CLASS, $currentClassOffset)) !== null) {
            if ($this->tokens[$currentClassOffset - 1]->id === T_DOUBLE_COLON || $this->tokens[$currentClassOffset - 1]->id === T_NEW) {
                continue;
            }

            $token = $this->getNextToken(T_STRING, $currentClassOffset);

            $classes[] = join('\\', [$ns, $token->token]);
        }

        return $classes;
    }

    private function getNextToken(int $flag, int $start = 0) : ?Token
    {
        $offset = $this->getNextTokenOffset($flag, $start);

        if ($offset === null) {
            return null;
        }

        return $this->tokens[$offset];
    }

    private function getNextTokenOffset(int $flag, int $start = -1) : ?int
    {
        for ($i = $start + 1; $i < count($this->tokens); $i++) {
            if ($this->tokens[$i]->id === $flag) {
                return $i;
            }
        }

        return null;
    }
}
