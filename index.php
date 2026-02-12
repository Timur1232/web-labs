<?php

//////////////////////////////////////////////////////////////////////////
// ============================ Lab 1 Tour ============================ //
//////////////////////////////////////////////////////////////////////////
//                                                                      //
// 1. Start                     - index.php                             //
// 2. Router class              - app/core/router.php:15                //
// 3. RouteGroup class          - app/core/router.php:101               //
// 4. Request class             - app/core/request.php:51               //
// 5. URL class                 - app/core/request.php:5                //
// 6. View class                - app/core/view.php:31                  //
// 7. Layout function           - app/core/layout.php:25                //
// 8. Helper functions          - app/core/helpers.php:19               //
// 9. Controllers               - app/controllers/index.php:10          //
//                              - app/controllers/about_me.php:10       //
//                              - app/controllers/interests.php:12      //
//                              - app/controllers/study.php:14          //
//                              - app/controllers/photoalbum.php:13     //
//                              - app/controllers/callback.php:16       //
//                              - app/controllers/history.php:10        //
// 10. FormValidator class      - app/core/data_validator.php:242       //
// 11. DataValidator class      - app/core/data_validator.php:22        //
// 12. Photoalbum model         - app/models/photoalbum.php:18          //
// 13. Interests model          - app/models/interests.php:9            //
// 14. Interests model instance - app/models/instances/interests.php:17 //
// 15. Test results model       - app/models/test_result.php:9          //
// 16. Callback validator model - app/models/callback_validator.php:9   //
// 17. Photoalbum view          - app/views/photoalbum.php:11           //
// 18. Callback view            - app/views/callback.php:9              //
// 19. Templates                                                        //
//                                                                      //
//////////////////////////////////////////////////////////////////////////

require_once 'app/core/core.php';
require_once 'app/core/active_record.php';
require_once 'app/models/scsv_ar_model.php';
require_once 'app/controllers/controllers.php';

use App\Core\ARAttributes;
use App\Core\ARField;
use App\Core\ActiveRecord;
use App\Core\Router;
use App\Controllers\{
    Index, AboutMe, Interests, Study, Photoalbum, Callback, History,
};
use App\Models\FileSCSVModel;
use function App\Core\Helpers\var_dump_preln;

#[ActiveRecord('test')]
class Test {
    #[ARField('a', ARField::ID_FIELD)]
    public int $a_field;
    #[ARField('b')]
    public string $b_field;
}

$model = FileSCSVModel::open('test_test.inc');
$model->read_all();

// DB::init_connection(new Sqlite(DB::sqlite_dns('./test.db')));

$rec = new Test();
$rec->a_field = 101010101;
// $rec->b_field = '!haey lleh';
// var_dump_preln($model->delete_by_id(Test::class, $rec->a_field));

// var_dump_preln($model->find_by_id(Test::class, 420));
var_dump_preln($model->find_all(Test::class));
die();

$router = Router::default();

$router->GET('/',             Index::index(...));
$router->GET('/about_me',     AboutMe::index(...));
$router->GET('/interests',    Interests::index(...));

$study = $router->group('/study');
$study->GET('/',              Study::index(...));
$study->GET('/test',          Study::test(...));
$study->POST('/test',         Study::check_test(...));

$router->GET('/photoalbum',   Photoalbum::index(...));
$router->GET('/callback',     Callback::index(...));
$router->GET('/history',      History::index(...));

$api = $router->group('/api');
// TODO: maybe separate data validating and accepting
$api->POST('/callback',       Callback::check(...));

$router->dispatch();
