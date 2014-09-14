File input and the new request objects
===

When using the new request objects with file inputs result i weird problems.

Go to the / route and there will be two forms for testing the same thing with
and without a request object.

For some reason errors are not showing up on this installation for me,
I'm just getting blank pages.

The issues showing up on this liferaft example is the same as i'm getting on
one of my other projects, there i get the following exception thrown when
submitting a form with an empty file input field:

InvalidArgumentException thrown with message "An uploaded file must be an array or an instance of UploadedFile."

Stacktrace:
#26 InvalidArgumentException in /home/vagrant/Code/jarvis/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/FileBag.php:59
#25 Symfony\Component\HttpFoundation\FileBag:set in /home/vagrant/Code/jarvis/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/FileBag.php:73
#24 Symfony\Component\HttpFoundation\FileBag:add in /home/vagrant/Code/jarvis/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/FileBag.php:48
#23 Symfony\Component\HttpFoundation\FileBag:replace in /home/vagrant/Code/jarvis/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/FileBag.php:37
#22 Symfony\Component\HttpFoundation\FileBag:__construct in /home/vagrant/Code/jarvis/vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/Request.php:245
#21 Symfony\Component\HttpFoundation\Request:initialize in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Foundation/Providers/FormRequestServiceProvider.php:57
#20 Illuminate\Foundation\Providers\FormRequestServiceProvider:initializeRequest in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Foundation/Providers/FormRequestServiceProvider.php:35
#19 Illuminate\Foundation\Providers\FormRequestServiceProvider:Illuminate\Foundation\Providers\{closure} in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Container/Container.php:839
#18 Illuminate\Container\Container:fireCallbackArray in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Container/Container.php:824
#17 Illuminate\Container\Container:fireResolvingCallbacks in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Container/Container.php:565
#16 Illuminate\Container\Container:make in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Foundation/Application.php:472
#15 Illuminate\Foundation\Application:make in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/RouteDependencyResolverTrait.php:55
#14 Illuminate\Routing\ControllerDispatcher:resolveMethodDependencies in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/RouteDependencyResolverTrait.php:34
#13 Illuminate\Routing\ControllerDispatcher:resolveClassMethodDependencies in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:97
#12 Illuminate\Routing\ControllerDispatcher:call in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:66
#11 Illuminate\Routing\ControllerDispatcher:dispatch in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/Route.php:155
#10 Illuminate\Routing\Route:dispatchToController in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/Route.php:129
#9 Illuminate\Routing\Route:run in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/Router.php:1030
#8 Illuminate\Routing\Router:dispatchToRoute in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Routing/Router.php:996
#7 Illuminate\Routing\Router:dispatch in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Foundation/Application.php:812
#6 Illuminate\Foundation\Application:dispatch in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Foundation/Application.php:789
#5 Illuminate\Foundation\Application:handle in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Session/Middleware.php:72
#4 Illuminate\Session\Middleware:handle in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Cookie/Queue.php:47
#3 Illuminate\Cookie\Queue:handle in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Cookie/Guard.php:51
#2 Illuminate\Cookie\Guard:handle in /home/vagrant/Code/jarvis/vendor/stack/builder/src/Stack/StackedHttpKernel.php:23
#1 Stack\StackedHttpKernel:handle in /home/vagrant/Code/jarvis/vendor/laravel/framework/src/Illuminate/Foundation/Application.php:665
#0 Illuminate\Foundation\Application:run in /home/vagrant/Code/jarvis/public/index.php:49

