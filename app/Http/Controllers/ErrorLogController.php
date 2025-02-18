<?php

/**
 * File ErrorLogController.php
 *
 * Manage all the task related operation in this class and return the response
 * PHP Version 8.0
 *
 * @category  ErrorLogController
 * @package   ErrorLogController
 * @author    SchellBrothers <schellbrothers@schellbrothers.com>
 * @copyright 2023 SchellBrothers
 * @license   https://schellbrothers.com (SchellBrothers )
 * @link      tag in file comment
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreErrorLogRequest;
use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Stores the configuration used to run PHPCS and PHPCBF.
 *
 * Save ErrorLogController and show all data in views
 * PHP Version 8.0
 *
 * @category  ErrorLogController
 * @package   ErrorLogController
 * @author    SchellBrothers <schellbrothers@schellbrothers.com>
 * @copyright 2023 SchellBrothers
 * @license   https://schellbrothers.com (SchellBrothers )
 * @link      tag in file commen
 */

class ErrorLogController extends Controller
{
    /**
     * Method getAllErrorLogs
     *
     * Method getAllErrorLogs return all the error log record
     *
     * @return void
     */
    public function getAllErrorLogs()
    {
        try {
            $allTask = ErrorLog::all();
            if (count($allTask)) {
                return response()->json(
                    [
                        'status' => Response::HTTP_OK,
                        'message' => trans('messages.Select'),
                        'data' => $allTask,
                    ]
                );
            } else {
                return response()->json(
                    [
                        'status' => Response::HTTP_OK,
                        'message' => trans('messages.NotFound'),
                        'data' => null,
                    ]
                );
            }
        } catch (Throwable $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ]
            );
        }
    }

    /**
     * Method getSingleErrorLog
     *
     * Method getSingleErrorLog return the particular log record by Id
     *
     * @param $id $id [explicite description]
     *
     * @return void
     */
    public function getSingleErrorLog($id)
    {
        try {
            $taskDetail = ErrorLog::findorFail($id);
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.Select'),
                    'data' => $taskDetail,
                ]
            );
        } catch (ModelNotFoundException $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.NotFound'),
                    'data' => null,
                ]
            );
        } catch (Throwable $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ]
            );
        }

    }

    /**
     * Method storeErrorLog
     *
     * Method storeErrorLog save the task record
     *
     * @param StoreErrorLogRequest $request [explicite description]
     *
     * @return void
     */
    public function storeErrorLog(StoreErrorLogRequest $request)
    {
        try {
            $log = new ErrorLog();
            $log->microservice = $request->microservice;
            $log->method = $request->method;
            $log->endpoint = $request->endpoint;
            $log->payload = json_encode($request->payload);
            $log->status = $request->status;
            $log->error = $request->error;
            $log->line = $request->line;
            $log->file = $request->file;
            $log->message = $request->message;
            $log->save();
            return response()->json(
                [
                    'status' => Response::HTTP_CREATED,
                    'message' => trans('messages.Insert'),
                    'data' => $log->id,
                ], Response::HTTP_CREATED
            );
        } catch (Throwable $e) {
            Log::info($e);
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ], Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Method updateErrorLog
     *
     * Method updateErrorLog update the particular task record by Id
     *
     * @param StoreErrorLogRequest $request [explicite description]
     * @param $id $id [explicite description]
     *
     * @return void
     */
    public function updateErrorLog(StoreErrorLogRequest $request, $id)
    {
        try {
            $updateTask = ErrorLog::findorFail($id);
            if ($updateTask) {
                $updateTask->fill($request->all())->save();
                return response()->json(
                    [
                        'status' => Response::HTTP_OK,
                        'message' => trans('messages.Update'),
                        'data' => $updateTask,
                    ]
                );
            }
        } catch (ModelNotFoundException $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.NotFound'),
                    'data' => null,
                ]
            );
        } catch (Throwable $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ]
            );
        }

    }

    /**
     * Method updateErrorLogPartial
     *
     * Method updateErrorLogPartial update the particular task record by Id
     *
     * @param StoreTaskRequest $request [explicite description]
     * @param $id $id [explicite description]
     *
     * @return void
     */
    public function updateErrorLogPartial(StoreErrorLogRequest $request, $id)
    {
        try {
            $updateTask = ErrorLog::findorFail($id);
            //get table columns
            $payload = $request->all();
            $tableColumns = $updateTask->getConnection()->getSchemaBuilder()->getColumnListing($updateTask->getTable());
            $matches = true;
            foreach (array_keys($payload) as $value) {
                if (!in_array($value, $tableColumns)) {
                    $matches = false;
                    break;
                }
            }
            if (!$matches) {
                return response()->json(
                    [
                        'status' => Response::HTTP_OK,
                        'message' => trans('messages.ColumnNotFound'),
                        'data' => null,
                    ]
                );
            }

            if ($updateTask) {
                $updateTask->update($request->only('microservice', 'method', 'endpoint', 'payload', 'status', 'error', 'line', 'file', 'message'));
                return response()->json(
                    [
                        'status' => Response::HTTP_OK,
                        'message' => trans('messages.Update'),
                        'data' => $updateTask,
                    ]
                );
            }
        } catch (ModelNotFoundException $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.NotFound'),
                    'data' => null,
                ]
            );
        } catch (Throwable $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ], Response::HTTP_BAD_REQUEST
            );
        }

    }

    /**
     * Method destroyErrorLog
     *
     * Method destroyErrorLog delete the particular error log record by Id
     *
     * @param Request $request [explicite description]
     * @param $id $id [explicite description]
     *
     * @return void
     */
    public function destroyErrorLog(Request $request, $id)
    {
        try {
            $taskBank = ErrorLog::findOrFail($id);
            $taskBank->delete();
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.Delete'),
                ]
            );
        } catch (ModelNotFoundException $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.NotFound'),
                    'data' => null,
                ]
            );
        } catch (Throwable $e) {
            Log::info($e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ]
            );
        }

    }

    /**
     * Method storeWebhookCall
     *
     * Method storeWebhookCall save the requested payload from the Webhooks 
     *
     * @param Request $request [explicite description]
     *
     * @return void
     */
    public function storeWebhookCall(Request $request)
    {
        try {
            $log = new ErrorLog();
            $log->microservice = $request->module;
            $log->method = $request->method;
            $log->endpoint = "http://127.0.0.1:8010/api/schedule";
            $log->payload = json_encode($request->payload);
            $log->status = "400";
            $log->error = "0";
            $log->line = "50";
            $log->file = "Default";
            $log->message = $request->message;
            $log->save();
            return response()->json(
                [
                    'status' => Response::HTTP_OK,
                    'message' => trans('messages.Insert'),
                    'data' => $log->id,
                ], Response::HTTP_OK
            );
        } catch (Throwable $e) {
            Log::info($e);
            return response()->json(
                [
                    'error' => $e->getMessage(),
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => trans('messages.SomethingWrong'),
                ], Response::HTTP_BAD_REQUEST
            );
        }
    }

}
