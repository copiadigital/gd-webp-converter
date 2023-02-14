<?php

namespace GdWebpConverter\Providers;

class GdWebpConverterServiceProvider implements Provider
{
    protected function providers()
    {
        return [
            GdDefaultServiceProvider::class,
            ConverterServiceProvider::class,
            FieldsServiceProvider::class,
            ConvertAttachmentServiceProvider::class,
        ];
    }

    public function register()
    {
        foreach ($this->providers() as $service) {
            (new $service)->register();
        }
    }

    public function boot()
    {
        //
    }
}
