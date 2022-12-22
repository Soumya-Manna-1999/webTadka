<?php

namespace App\Http\Controllers;

use App\Models\blog_categories;
use App\Models\blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class blogController extends Controller
{
    public function blogList()
    {
        view()->share(['pageTitle' => 'Blog List']);
        return view('admin_dashboard.blog.blogList');
    }

    public function createBlog()
    {
        $blogCategory = blog_categories::all();
        view()->share([
            'pageTitle' => 'Create New Blog',
            'blogCategory' => $blogCategory,
        ]);
        return view('admin_dashboard.blog.create_new_blog');
    }

    public function uploadBlogImage(Request $request)
    {
    }

    public function addBlogCategory(Request $request)
    {
        if ($request->category_id != '') {
            $checkIfExist = blog_categories::find($request->category_id);
            if (!empty($checkIfExist)) {
                $checkIfExist->category_name = $request->category_name;
                $isSuccess = $checkIfExist->save();
                if ($isSuccess) {
                    return response()->json([
                        'status' => true,
                        'message' => "Successfully added category."
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Spmething Went Wrong, Please Contact developer."
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Can't Update Category.Blog category not found"
                ]);
            }
        } else {
            $checkIfExist = blog_categories::where('category_name', $request->category_name)->first();
            if (!empty($ifExist)) {
                return response()->json([
                    'status' => false,
                    'message' => "This blog category already exists."
                ]);
            } else {
                $isSuccess = blog_categories::create([
                    'category_name' => $request->category_name
                ])->save();
                if ($isSuccess) {
                    return response()->json([
                        'status' => true,
                        'message' => "Successfully added category."
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Something Went Wrong, Please Contact developer."
                    ]);
                }
            }
        }
    }

    public function saveBlog(Request $request)
    {
        if ($request->blogid == '') {
            $isSuccess = blogs::create([
                'author' => 'WebTadka',
                'heading' => $request->blogname,
                'description' => $request->blog,
                'blog_category' => $request->category,
                'meta_title' => $request->metatitle,
                'meta_description' => $request->metadescription,
                'image' => '',
            ])->save();
            if ($isSuccess) {
                return response()->json([
                    'status' => true,
                    'message' => "Blog Published Successfully."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Something Went Wrong, Please Contact developer."
                ]);
            }
        }
    }
}