<?php

/**
 * Class Rate
 */
class Rate
{

    /**
     * @var array
     */
    public $result = [
        'general' => []
    ];

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
    public function checkPhpDocs(string $file)
    {
        // Message if start !== end
        // message if every function, every attribute, every const and every class has none
        $this->result['phpDocs'] = [];
        $this->result['phpDocs']['start'] = substr_count($file, '/**');
        $this->result['phpDocs']['anotations'] = substr_count($file, '* @');
        $this->result['phpDocs']['end'] = substr_count($file, '*/');
    }

    public function checkNamespace(string $file)
    {
        // message if not 1
        $this->result['general']['namespace'] = substr_count($file, 'namespace ');
    }

    public function checkUse(string $file)
    {
        // message if not 1
        $this->result['general']['use'] = substr_count($file, 'use ');
    }

    public function checkConst(string $file)
    {
        // message if not 1 or more
        $this->result['general']['const'] = substr_count($file, 'const ');
    }

    public function isClass(string $file)
    {
        $tmp = explode(PHP_EOL, $file);
        // message not found in 5 lines
        // check lines before execute

        for ($i = 0; $i < 5; $i++) {
            if (substr_count(strtolower($tmp[$i]), 'class ')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $file
     */
    public function checkFile(string $filename): void
    {
        $file = $this->getFile($filename);

        if ($this->isClass($file)) {
            $this->checkNamespace($file);
            $this->checkUse($file);
            $this->checkLines($file);
            $this->checkFunctions($file);
            $this->checkAttributes($file);
            $this->checkConst($file);
            $this->checkPhpDocs($file);

            $this->files[$this->normalizeFileName($filename)] = $this->result;
            $this->resetResult();
        } else {
            // Message not checked becasue not class
        }
    }

    /**
     *
     */
    public function resetResult()
    {
        $this->result = [];
    }

    /**
     * @param string $filename
     * @return bool|false|string
     */
    public function getFile(string $filename)
    {
        $file = false;
        if (file_exists($filename) && !is_dir($filename)) {
            $file = file_get_contents($filename);
        }
        return $file ? $file : false;
    }

    /**
     * @param string $file
     */
    public function checkLines(string $file)
    {
        $this->result['lines'] = substr_count($file, PHP_EOL);
    }

    /**
     * @param string $file
     */
    public function checkFunctions(string $file)
    {
        $this->result['function'] = [];
        // message when to long
        $this->checkFunctionsPublic($file);
        $this->checkFunctionsPrivate($file);
        $this->checkFunctionsProtected($file);
    }

    /**
     * @param $file
     */
    public function checkFunctionsPublic($file)
    {
        preg_match_all("(public function)",
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['function']['public'] = count($matches[0]);
    }

    /**
     * @param $file
     */
    public function checkFunctionsPrivate($file)
    {
        preg_match_all("(private function)",
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['function']['private'] = count($matches[0]);
    }

    /**
     * @param $file
     */
    public function checkFunctionsProtected($file)
    {
        preg_match_all("(protected function)",
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['function']['protected'] = count($matches[0]);
    }

    /**
     * @param string $file
     */
    public function checkAttributes(string $file)
    {
        $this->result['attribute'] = [];
        $this->checkAttributesPublic($file);
        $this->checkAttributesProtected($file);
        $this->checkAttributesPrivate($file);
    }

    /**
     * @param $file
     */
    public function checkAttributesPublic($file)
    {
        preg_match_all('(public \$)',
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['attribute']['public'] = count($matches[0]);
    }

    /**
     * @param $file
     */
    public function checkAttributesProtected($file)
    {
        preg_match_all('(protected \$)',
            $file,
            $matches, PREG_PATTERN_ORDER);

        $this->result['attribute']['protected'] = count($matches[0]);
    }

    /**
     * @param $file
     */
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
    public function checkModule(string $path): void
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
        // generate modules list
        // check with checkModule
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
