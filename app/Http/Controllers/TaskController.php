<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\FilterRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    use ApiResponse ;

    public function __construct()
    {
        $this->task = new Task();
    }

    public function addTask(TaskRequest $request)
    {
        $input = $request->all();
        // $output = $this->task->create($request->only(['title' , 'description' , 'due_date' , "parent_task_id"]));
        $output = $this->task->create($input);
        if($output)
        {
            return self::ResponseSuccess(data: ["task" => new TaskResource($output->fresh())]);
        }
    }

    public function updateTask(TaskRequest $request , $id)
    { 
        $input = $request->all();
        $output = $this->task->findOrFail($id);
        $updated = $output->update($input);
        if($updated)
        {
            return self::ResponseSuccess(data: ["task" => new TaskResource($output)]);
        }else{
            return self::ResponseFail(message : " the data is not updated .");
        }
    }

    public function showTask($id)
    {
        $data = $this->task->with("employee:id,full_name,email","taskDependencies.employee:id,full_name")->findOrFail($id);
        return self::ResponseSuccess(data: ["task" => new TaskResource($data)]);
    }

    public function listAllTasks(FilterRequest $request)
    {
        $data = $this->task->with("employee:id,full_name,email")
                    ->when(filled($request['status']), function ($q) use ($request) {
                        $q->where('status', $request['status']);
                        })->when(filled($request['assigned_name']), function ($q) use ($request) {
                            $q->whereHas('employee', function ($q) use ($request) {
                                $q->where("full_name", 'LIKE', '%'. $request['assigned_name'] .'%');
                            });
                        })->when(filled($request['from']) && filled($request['to']), function ($q) use ($request) {
                            return $q->whereBetween('due_date', [$request['from'], $request['to']]);
                        })
                    ->orderBy('created_at','desc')->get();

        return self::ResponseSuccess(data: ["tasks" => $data]);
    }

    public function listEmployeeTasks()
    {
        $employee_id = Auth()->user()->id ;

        $data = $this->task->where("employee_id",$employee_id)->select("id","title","description" ,"due_date" , "status")->orderBy("created_at",'desc')->get();
        return self::ResponseSuccess(data : ["tasks" => $data]);
    }

    public function updateTaskStatus(Request $request , $id)
    {
        $rules = array(
            'status' => ["required" , "string" ,Rule::in(["cancelled" , "completed"])],
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return self::ResponseFail(message: $validator->errors()->first());
        }else{
            $data = $this->task->findOrFail($id);
            $unfinished_tasks = $this->task->where("parent_task_id", $id)->where('status' , "pending")->get();
            if(filled($unfinished_tasks))
            {
                return self::ResponseFail(message : " can not update this task status since its dependencies are not completed yet");
            }else if($data->employee_id == Auth()->user()->id){
                $updated = $data->update(['status' => $request->status]);
                return self::ResponseSuccess(data : ["task" => new TaskResource($data)]);
            }else{
                return self::ResponseFail(message : " can not update this task status since it is not your task");
            }
        }
    }

    public function assignTask(Request $request)
    {
        $rules = array(
            "tasks" => "array|required" ,
            "tasks.*" => "integer|exists:tasks,id" ,
            'employee_id' => 'required|integer|exists:users,id' , 
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return self::ResponseFail(message: $validator->errors()->first());
        }else{
            $data = $this->task->whereIn("id" , $request['tasks']);
            $updated = $data->update(['employee_id' => $request->employee_id]);
            if($updated)
            {
                return self::ResponseSuccess(data: ["task" => TaskResource::collection($data->get())]);
            }else{
                return self::ResponseFail(message : " the task is not assigned yet .");
            }
        }
    }
}
