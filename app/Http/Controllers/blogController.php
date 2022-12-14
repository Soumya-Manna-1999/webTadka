<?php

namespace App\Http\Controllers;

use App\Models\blog_categories;
use App\Models\blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class blogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function blogList()
    {
        $blogs = blogs::with('blogCategory')->get();
        view()->share([
            'pageTitle' => 'Blog List',
            'blogs' => $blogs,
        ]);
        return view('admin_dashboard.blog.blogList');
    }

    public function createBlog()
    {
        $blogCategory = blog_categories::all();
        $blogname = '';
        $blog = '';
        $category = '';
        $image = '';
        $metatitle = '';
        $blog_id = '';
        $metadescription = '';
        $pageTitle = 'Create New Blog';
        $author = '';
        if (isset($_GET['data'])) {
            $decryptedData = json_decode(Crypt::decryptString($_GET['data']));
            $blogname = $decryptedData->heading;
            $blog = $decryptedData->description;
            $category = $decryptedData->blog_category->id;
            $image = $decryptedData->image;
            $metatitle = $decryptedData->meta_title;
            $metadescription = $decryptedData->meta_description;
            $blog_id = $decryptedData->id;
            $pageTitle = "Update Blog";
            $author = $decryptedData->author;
        }
        $blogData = [
            'pageTitle' => $pageTitle,
            'blogCategory' => $blogCategory,
            'blogheading' => $blogname,
            'blog' => $blog,
            'category' => $category,
            'image' => $image,
            'metatitle' => $metatitle,
            'metadescription' => $metadescription,
            'blogid' => $blog_id,
            'author' => $author,
        ];
        view()->share($blogData);
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
                'author' => $request->author,
                'heading' => $request->blogname,
                'description' => $request->blog,
                'blog_category' => $request->category,
                'meta_title' => $request->metatitle,
                'meta_description' => $request->metadescription,
                'image' => $this->imageLinkGenerator($request),
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
        } else {
            $getBlogData = blogs::find($request->blogid);
            if (!empty($getBlogData)) {
                $image = '';
                if ($request->thumbnail != '') {
                    if ($getBlogData->image != '')
                        unlink(public_path($getBlogData->image));
                    $image = $this->imageLinkGenerator($request);
                } else
                    $image = $getBlogData->image;
                $getBlogData->author = $request->author;
                $getBlogData->heading = $request->blogname;
                $getBlogData->description = $request->blog;
                $getBlogData->blog_category = $request->category;
                $getBlogData->meta_title = $request->metatitle;
                $getBlogData->meta_description = $request->metadescription;
                $getBlogData->image = $image;
                $isSuccess = $getBlogData->save();
                if ($isSuccess) {
                    return response()->json([
                        'status' => true,
                        'message' => "Blog Edited Successfully."
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Something Went Wrong, Please Contact developer."
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Can'r edit the blog, Blog Not exists."
                ]);
            }
        }
    }

    public function imageLinkGenerator($request)
    {
        $image = $request->file('thumbnail');
        $extention = explode('.', $image->getClientOriginalName());
        $input['imagename'] = time() . '_' . Str::random(5) . '_blog_thumbnail.' . $extention[1];
        $destinationPath = public_path('/document_bucket');
        $image->move($destinationPath, $input['imagename']);
        $finalImageUrl = '/document_bucket/' . $input['imagename'];
        return $finalImageUrl;
    }

    public function deleteBlog(Request $request)
    {
        $search = blogs::find($request->id);
        if (!empty($search)) {
            if ($search->image != '' && $search->image != null)
                unlink(public_path($search->image));
            $is_success = $search->delete();
            if ($is_success)
                return response()->json([
                    'status' => true,
                    'message' => "Successfully deleted the blog."
                ]);
            else
                return response()->json([
                    'status' => false,
                    'message' => "Something Went Wrong, Please Contact developer."
                ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Can't delete this blog. This blog is invalid"
            ]);
        }
    }
}
