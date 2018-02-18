<?php

# example routing file

return [
    [['GET'], '/', 'PSX\Framework\Loader\Foo1Controller'],
    [['GET'], '/foo/bar', 'PSX\Framework\Loader\Foo2Controller'],
    [['GET'], '/foo/:bar', 'PSX\Framework\Loader\Foo3Controller'],
    [['GET'], '/foo/:bar/:foo', 'PSX\Framework\Loader\Foo4Controller'],
    [['GET'], '/bar', 'PSX\Framework\Loader\Foo5Controller'],
    [['GET'], '/bar/foo', 'PSX\Framework\Loader\Foo6Controller'],
    [['GET'], '/bar/$foo<[0-9]+>', 'PSX\Framework\Loader\Foo7Controller'],
    [['GET'], '/bar/$foo<[0-9]+>/$bar<[0-9]+>', 'PSX\Framework\Loader\Foo8Controller'],
    [['POST'], '/bar', 'PSX\Framework\Loader\Foo9Controller'],
    [['GET'], '/whitespace', 'PSX\Framework\Loader\Foo10Controller'],
    [['GET', 'POST'], '/test', 'PSX\Framework\Loader\Foo11Controller'],
    [['GET'], '/alias', '~/foo/bar'],
    [['GET'], '/files/*path', 'PSX\Framework\Loader\Foo12Controller'],
    [['GET'], 'http://cdn.foo.com/serve/*path', 'PSX\Framework\Loader\Foo13Controller'],
    [['ANY'], '/baz', 'PSX\Framework\Loader\Foo14Controller'],
    
    [['ANY'], '/baz', [
        
        'foo_bar'
        
        
    ]],
];
