<?php
namespace XYZSP\Core;


use XYZSP\Admin\ProductFields;
use XYZSP\Admin\VariationFields;
use XYZSP\Views\PriceHandler;

final class Plugin
{
    private static $instance = null;
    private $file;


    private function __construct(string $file)
    {
        $this->file = $file;
        $this->load_dependencies();
    }


    public static function instance(string $file): self
    {
        if (self::$instance === null) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }

    public static function activate()
    {
        // Ensure the "supplier" role exists. Create it if it doesn't, with minimal permissions.
        if (!get_role('supplier')) {
            add_role(
                'supplier',
                'Supplier',
                ['read' => true, 'level_0' => true]
            );
        }
    }



    private function load_dependencies(): void
    {
        // nothing heavy here; classes are autoloaded via PSR-4
    }


    public function run(): void
    {
        // Initialize components
        if (is_admin()) {
            ProductFields::init();
            VariationFields::init();
        }


        // Frontend price handler
        PriceHandler::init();
    }
}