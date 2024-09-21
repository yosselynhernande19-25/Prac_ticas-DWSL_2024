<?php

class TemplateEngine {
    
    protected $file;
    protected $sections = [];
    protected $sectionStack = [];

    public function __construct($file) {
        if (!file_exists($file)) {
            throw new Exception("El archivo de plantilla '$file' no existe.");
        }

        $this->file = $file; 
    }

    public function render($data = []) {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Los datos deben ser un array.");
        }

        extract($data);

        ob_start();
        include $this->file;
        $content = ob_get_clean();

        $content = $this->parseDirectives($content);

        eval('?>' . $content);
    }

    protected function parseDirectives($content) {
        $content = preg_replace('/@php(.*?)@endphp/s', '<?php $1 ?>', $content);

        $content = preg_replace_callback('/{{\s*\$([^}]+)\s*}}/', function($matches) {
            return '<?php echo $' . $matches[1] . '; ?>';
        }, $content);

        $content = preg_replace_callback('/@if\s*\(([^)]+)\)(.*?)@else(.*?)@endif/s', function($matches) {
            return '<?php if (' . $matches[1] . '): ?>' . $matches[2] . '<?php else: ?>' . $matches[3] . '<?php endif; ?>';
        }, $content);

        $content = preg_replace_callback('/@extends\(\'([^\']+)\'\)/', function($matches) {
            return file_get_contents($matches[1]);
        }, $content);

        $content = preg_replace_callback('/@foreach\s*\(([^)]+)\)\s*(.*?)\s*@endforeach/', function($matches) {
            return '<?php foreach (' . $matches[1] . '): ?>' . $matches[2] . '<?php endforeach; ?>';
        }, $content);

        $content = preg_replace_callback('/@section\(\'([^\']+)\'\)(.*?)@endsection/s', function($matches) {
            $this->section($matches[1], $matches[2]);
            return '';
        }, $content);

        $content = preg_replace_callback('/@yield\(\'([^\']+)\'\)/', function($matches) {
            return isset($this->sections[$matches[1]]) ? $this->sections[$matches[1]] : '';
        }, $content);

        $content = preg_replace_callback('/{{\s*asset\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*}}/', function($matches) {
            return $this->asset($matches[1]);
        }, $content);

        return $content;
    }

    public function startSection($name) {
        ob_start();
        $this->sectionStack[] = $name;
    }

    public function endSection() {
        $name = array_pop($this->sectionStack);
        $this->sections[$name] = ob_get_clean();
    }

    public function section($name, $content = null) {
        if ($content === null) {
            $this->startSection($name);
        } else {
            $this->sections[$name] = $content;
        }
    }

}
?>