<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CharactersController extends Controller
{
    public function index(Request $request)
    {
        return view('characters', [
            'characters' => $request->user()->characters
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
            ],
            'name' => ['required']
        ]);

        DB::transaction(function () use ($request) {
            $file = $request->file("file");

            $c = new Character();
            $c->id = $uuid = Str::uuid()->toString();
            $c->name = $request->post("name");
            $c->description = $request->post("description");
            $c->user()->associate($request->user());

            $path = "/user_{$request->user()->id}/characters/{$uuid}.{$file->extension()}";
            $c->image_path = "/storage".$path;
            $c->save();

            $file->storePubliclyAs("public".Str::beforeLast($path, "/"), Str::afterLast($path, "/"));
        });

        return view('characters', [
            'characters' => $request->user()->characters
        ]);
    }
}
