<?php

/**
 * Class Rate
 */
class Rate
{

    /**
     * @var array
     */
    public $result = [];

    /**
     * @var string
     */
    public $type = 'file';

    /**
     * @var array
     */
    public $files = [];

    /**
     * Rate constructor.
     * @param $data
     */
    public function __construct($data)
    {
        if (is_string($data)) {
            $this->type = 'file';
            $this->checkFile($data);
        } else {
            $this->type = 'folder';
            $this->checkFolder($data);
        }
    }

    /**
     * @param string $file
     */
    public function checkFile(string $filename): void
    {
        $file = $this->getFile($filename);

        $this->checkLines($file);
        $this->checkFunctions($file);
        $this->checkAttributes($file);

        var_dump($this->result);
        $this->files[$this->normalizeFileName($filename)] = $this->result;
    }

    public function getFile(string $filename)
    {
        $file = false;
        if (file_exists($filename) && !is_dir($filename)) {
            $file = file_get_contents($filename);
        }
        return $file ? $file : false;
    }

    public function checkLines(string $file)
    {
        $this->result['lines'] = substr_count($file, PHP_EOL);
    }

    public function checkFunctions(string $file)
    {
        $this->result['function'] = [];
        $this->checkFunctionsPublic($file);
        $this->checkFunctionsPrivate($file);
        $this->checkFunctionsProtected($file);
    }

    public function checkFunctionsPublic($file)
    {
        preg_match_all("(public function)",
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['function']['public'] = count($matches[0]);
    }

    public function checkFunctionsPrivate($file)
    {
        preg_match_all("(private function)",
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['function']['private'] = count($matches[0]);
    }

    public function checkFunctionsProtected($file)
    {
        preg_match_all("(protected function)",
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['function']['protected'] = count($matches[0]);
    }

    public function checkAttributes(string $file)
    {
        $this->result['attribute'] = [];
        $this->checkAttributesPublic($file);
        $this->checkAttributesProtected($file);
        $this->checkAttributesPrivate($file);
    }

    public function checkAttributesPublic($file)
    {
        preg_match_all('(public \$)',
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['attribute']['public'] = count($matches[0]);
    }

    public function checkAttributesProtected($file)
    {
        preg_match_all('(protected \$)',
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['attribute']['protected'] = count($matches[0]);
    }

    public function checkAttributesPrivate($file)
    {
        preg_match_all('(private \$)',
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['attribute']['private'] = count($matches[0]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function normalizeFileName(string $name): string
    {
        $name = str_replace(['/', '.'], ['-', '-'], $name);

        return $name;
    }

    /**
     * @param string $path
     */
    public function checkFolder(string $path): void
    {
        // implement logic
    }

    /**
     * @param string $path
     */
    public function checkModules(string $path): void
    {
        // implement logic
        // add hint for module size
    }

    /**
     * @return string
     */
    public function report(): string
    {
        // create phar out of script
        $this->result();
        return json_encode($this->result, JSON_FORCE_OBJECT);
    }

    /**
     *
     */
    public function result(): void
    {
        $this->result = $this->files;
    }
}
