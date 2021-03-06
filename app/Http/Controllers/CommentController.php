<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use Illuminate\Support\Facades\Auth;
use App\TinTuc;
use App\AuthTraits\CanManageTrait;
class CommentController extends Controller
{
    use CanManageTrait;
    public function getPhanHoi(){
        if(Auth::user()->IsModOrAdmin())
            {
                $binhluan = Comment::orderBy('created_at', 'DESC')->get();
            }
        else
        {
            $binhluan = collect();
            $tintuc = Auth::user()->tintuc()->get();
            foreach ($tintuc as $tt) {
                $bl = $tt->comment()->get();
                $binhluan = $binhluan->concat($bl);
            }
            
        }
        return view('admin.comment.danhsach',['binhluan'=>$binhluan]);
    }

    public function getXoa($id, $idTinTuc){
        $loaitin = Comment::find($id);
        $loaitin->delete();

        return redirect('admin/tintuc/sua/'.$idTinTuc)->with('thongbao', 'Xóa comment thành công');
    }

    public function getXoaBL($id){
        $loaitin = Comment::find($id);
        $loaitin->delete();

        return redirect('admin/comment/danhsach')->with('thongbao', 'Xóa comment thành công');
    }

    public function postComment($id, Request $request){     
    	$idTinTuc = $id;
    	$tintuc = TinTuc::find($id);
    	$comment = new Comment;
    	$comment->idTinTuc = $idTinTuc;
    	$comment->idUser  = Auth::user()->id;
    	$comment->NoiDung = $request->NoiDung;
    	$comment->save();

    	return redirect("tintuc/$id/".$tintuc->TieuDeKhongDau.".html");
    }
}
