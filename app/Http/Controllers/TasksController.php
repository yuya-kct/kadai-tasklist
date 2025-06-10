<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function top()
        {
            if (Auth::check()) {
            // ログイン済みの場合、タスク一覧を表示
            $tasks = Task::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(25);
            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
            } else {
                // 未ログインの場合、dashboard.blade.php を表示
                return view('dashboard');
            }
        }
    public function index()
    {
        $tasks = Task::where('user_id',Auth::id())->orderBy('id', 'desc')->paginate(25);        
        // dashboardビューでそれらを表示
        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        //
        // 認証済みユーザー（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
        //$request->user()->tasks()->create([
            //'content' => $request->content,
            //'status' => $request->status,
        //]);

        $task = new Task;
        $task->status = $request->status;
        $task->content = $request->content;
        $task->user_id = Auth::id();
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        try {
            // 認証済みユーザーのタスクを取得
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            // タスク詳細ビューでそれを表示
            return view('tasks.show', [
                'task' => $task,
            ]);
        } catch (\Exception $e) {
            // 他のユーザーのタスクにアクセスしようとした場合、トップページにリダイレクト
            return redirect('/')->with('error', 'アクセス権がありません。');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function edit($id)
    {
        try {
            // 認証済みユーザーのタスクを取得
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            // タスク詳細ビューでそれを表示
            return view('tasks.edit', [
                'task' => $task,
            ]);
        } catch (\Exception $e) {
            // 他のユーザーのタスクにアクセスしようとした場合、トップページにリダイレクト
            return redirect('/')->with('error', 'アクセス権がありません。');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        try {
            // 認証済みユーザーのタスクを取得
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            
            // メッセージを更新
            $task->status = $request->status;
            $task->content = $request->content;
            $task->save();

        // トップページへリダイレクトさせる
            return redirect('/');
        } catch (\Exception $e) {
            // 他のユーザーのタスクにアクセスしようとした場合、トップページにリダイレクト
            return redirect('/')->with('error', 'アクセス権がありません。');
        }
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // 認証済みユーザーのタスクを取得
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            
            $task->delete();

            // トップページへリダイレクトさせる
            return redirect('/');
        } catch (\Exception $e) {
            // 他のユーザーのタスクにアクセスしようとした場合、トップページにリダイレクト
            return redirect('/')->with('error', 'アクセス権がありません。');
        }
    }
}
