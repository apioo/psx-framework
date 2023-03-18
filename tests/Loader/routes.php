<?php

# example routing file

return [
    [['GET'], '/', ['PSX\Framework\Loader\Foo1Controller', 'show']],
    [['GET'], '/foo/bar', ['PSX\Framework\Loader\Foo2Controller', 'show']],
    [['GET'], '/foo/:bar', ['PSX\Framework\Loader\Foo3Controller', 'show']],
    [['GET'], '/foo/:bar/:foo', ['PSX\Framework\Loader\Foo4Controller', 'show']],
    [['GET'], '/bar', ['PSX\Framework\Loader\Foo5Controller', 'show']],
    [['GET'], '/bar/foo', ['PSX\Framework\Loader\Foo6Controller', 'show']],
    [['GET'], '/bar/$foo<[0-9]+>', ['PSX\Framework\Loader\Foo7Controller', 'show']],
    [['GET'], '/bar/$foo<[0-9]+>/$bar<[0-9]+>', ['PSX\Framework\Loader\Foo8Controller', 'show']],
    [['POST'], '/bar', ['PSX\Framework\Loader\Foo9Controller', 'show']],
    [['GET'], '/whitespace', ['PSX\Framework\Loader\Foo10Controller', 'show']],
    [['GET', 'POST'], '/test', ['PSX\Framework\Loader\Foo11Controller', 'show']],
    [['GET'], '/alias', '~/foo/bar'],
    [['GET'], '/files/*path', ['PSX\Framework\Loader\Foo12Controller', 'show']],
    [['GET'], 'http://cdn.foo.com/serve/*path', ['PSX\Framework\Loader\Foo13Controller', 'show']],
    [['ANY'], '/baz', ['PSX\Framework\Loader\Foo14Controller', 'show']],
    [['ANY'], '/baz', ['foo_bar']],
];
