<?php


namespace Tests\Unit\View\Components;

use Tests\TestCase;


class AppLayoutTest extends TestCase
{
    /**
     * Test the render method of the AppLayout component.
     */
    public function testRenderMethod()
    {
        // Créez une instance du composant AppLayout
        $component = new \App\View\Components\AppLayout();

        // Appelez la méthode render pour obtenir la vue
        $view = $component->render();

        // Vérifiez si la vue est celle attendue (le chemin de vue 'layouts.app')
        $this->assertEquals('layouts.app', $view->getName());
    }
}