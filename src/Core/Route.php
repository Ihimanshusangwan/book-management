<?php
namespace Core;

use Attribute;
#[Attribute]
class Route {
    public string $method;
    public string $path;
    public ?string $role;

    public function __construct(string $method, string $path, ?string $role = null) {
        $this->method = $method;
        $this->path = $path;
        $this->role = $role;
    }
}
