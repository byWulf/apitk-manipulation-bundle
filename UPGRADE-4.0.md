# Upgrade from 3.x to 4.0

### Updated Requirements
1. **PHP >= 8.0** _(was >= 7.4 before)_
1. **Symfony >= 5.3** _(was >= 4.4 before)_
2. **NelmioApiDocBundle >= 4.0** _(was 3.x before)_
2. **zircote/swagger-php >= 4.0** _(was 2.x before)_

### Steps

1. Update `zircote/swagger-php` and `NelmioApiDocBundle` to their most recent 
   versions (latest 4.x releases).
2. This will force you to perform the Swagger -> OpenApi migration. Swagger has been 
   renamed to "Open API" and thus all namespaces have changed.    
   Most of the time it's sufficient to swap:

        use Swagger\Annotations as SWG;

   with

        use OpenApi\Annotations as OA;

   and use `@OA\...` instead of `@SWG\...`  annotations in your code.

   More about the migration from swagger to openapi is available in the
   [nelmio/api-doc-bundle migration guide] and the [zircote/swagger-php migration guide].



[nelmio/api-doc-bundle migration guide]: https://github.com/nelmio/NelmioApiDocBundle/blob/master/UPGRADE-4.0.md
[zircote/swagger-php migration guide]: https://zircote.github.io/swagger-php/Migrating-to-v3.html
