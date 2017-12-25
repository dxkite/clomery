<?php
namespace cn\atd3\proxy\exception;

class Handler
{
    public static function uncaughtException($exception)
    {
        if (request()->isJson() || request()->get('is_rpc_call')) {
            $response=new class extends \suda\core\Response {
                public $exception;
                public function onRequest(\suda\core\Request $request)
                {
                    $this->state(400);
                    debug()->logException($this->exception);
                    
                    $this->error($this->exception->getName(), $this->exception->getMessage());
                }

                protected function error(string $name, string $message, $data=null)
                {
                    $error=[
                        'error'=>[
                            'name'=>$name,
                            'message'=>$message,
                            'data'=>$data,
                        ],
                        'id'=>null
                    ];
                    if (conf('debug')) {
                        $error['error']['backtrace']=$this->exception->getBackTrace();
                    }
                    return $this->returnJson($error);
                }

                protected function returnJson(array $json)
                {
                    if ($callback=request()->get('jsonp_callback')) {
                        $this->type('js');
                        echo $callback.'('.json_encode($json).');';
                    } else {
                        return $this->json($json);
                    }
                }
            };
            $response->exception=$exception;
            $response->onRequest(request());
            return true;
        }
        return false;
    }
}
