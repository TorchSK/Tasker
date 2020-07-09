$(".admin_categories_list .accordion").nestedSortable({handle:".handle",items:".category",listType:"ul",disableParentChange:!1,stop:function(e,t){$data=[],$orders={},$parents={},$(".admin_categories_list .accordion .category").each(function(e,t){$orders[$(t).data("id")]=e,$parents[$(t).data("id")]=$(t).closest("li").parent().closest("li").data("id")}),$.ajax({method:"PUT",url:"/categories/setorder",data:{orders:$orders,parents:$parents}})}}),$(".pages_list").nestedSortable({handle:".handle",items:".item",listType:"ul",disableParentChange:!1,stop:function(e,t){$data=[],$orders={},$parents={},$(".pages_list .item").each(function(e,t){$orders[$(t).data("id")]=e,$parents[$(t).data("id")]=$(t).closest("li").parent().closest("li").data("id")}),$.ajax({method:"PUT",url:"/pages/setorder",data:{orders:$orders,parents:$parents}})}}),$(".change_cat_img_btn").click(function(){$("#category_image_dropzone").click()}),$("#xml_dropzone").dropzone({clickable:"#xml_upload_btn",success:function(e,t){$("#xml_url_input").val(t),$("#xml_url_input").data("external",0)}}),$("#catalogue_dropzone").dropzone({clickable:".catalogue_upload_btn",success:function(e,t){location.reload()}}),$("#sticker_dropzone").dropzone({clickable:".sticker_upload_btn",success:function(e,t){location.reload()}}),$("#catalogue_image_dropzone_1").dropzone({clickable:"#catalogue_image_btn_1",success:function(e,t){location.reload()}}),$("#catalogue_image_dropzone_2").dropzone({clickable:"#catalogue_image_btn_2",success:function(e,t){location.reload()}}),$("#catalogue_image_dropzone_3").dropzone({clickable:"#catalogue_image_btn_3",success:function(e,t){location.reload()}}),$("#product_detail_dropzone").dropzone({params:{_token:$('meta[name="csrf-token"]').attr("content")}}),$("#file_dropzone").dropzone({params:{_token:$('meta[name="csrf-token"]').attr("content")},success:function(e,t){location.reload()}}),$("#import_dropzone").dropzone({success:function(e,t){$table=$("#admin_import_results").find("table"),$row=$table.find("tbody tr"),$.each(t,function(e,t){$lastRow=$table.find("tbody tr:last-child"),$lastRow.find('td[col="name"]').html(t.name),$lastRow.find('td[col="code"]').html(t.code),$lastRow.find('td[col="maker"]').html(t.maker),console.log(t),$row.clone().appendTo($table)}),$table.find("tbody tr:last-child").remove()},params:{_token:$('meta[name="csrf-token"]').attr("content")}}),$("#category_image_dropzone").dropzone({success:function(e,t){this.removeAllFiles(!0),$categoryid=$("#category_image_div").data("categoryid"),$.ajax({method:"POST",url:"/category/"+$categoryid+"/image/confirmCrop",data:{filename:e.name},success:function(){location.reload()}})}}),$("#cover_dropzone").dropzone({clickable:["#admin_add_cover_change_image_btn","#cover_dropzone"],success:function(e,t){this.removeAllFiles(!0),$(".crop_preview").html("<img src=/"+t+" />").unbind("focus"),$(".admin_add_cover_under").css("display","flex"),$(".cover_image").show(),$("#cover_dropzone").hide(),$('#admin_add_cover_form input[name="filename"]').val(e.name),$("#admin_add_cover_change_image_btn").hide(),"cover"==$('input[name="type"]').val()?$aspect=$("#cover_ratio").text():$aspect=$("#banner_ratio").text(),$(".crop_preview img").cropper({guides:!1,viewMode:1,aspectRatio:$aspect,autoCropArea:1,crop:function(e){$('#admin_add_cover_form input[name="x"]').val(e.x),$('#admin_add_cover_form input[name="y"]').val(e.y),$('#admin_add_cover_form input[name="w"]').val(e.width),$('#admin_add_cover_form input[name="h"]').val(e.height)},preview:$(".cover_image .cover")}),$(".crop_ok").show().click(function(){$categoryid=$(this).data("categoryid"),$.ajax({method:"POST",url:"/category/"+$categoryid+"/image/confirmCrop",data:{filename:e.name,x:void 0,y:void 0,w:void 0,h:void 0},success:function(){location.reload()}})})}}),$(".ui.accordion").accordion({exclusive:!1}),$("#filterbar .ui.accordion").accordion({exclusive:!1,selector:{accordion:".accordion",title:".title",trigger:".title i",content:".content"}}),$(".admin_categories_list .ui.accordion").accordion({exclusive:!1,selector:{accordion:".accordion",title:".title",trigger:".name",content:".content"}}),$("#admin #grid .product.item").draggable({opacity:.6,helper:"clone"}),$(".categories .item").droppable({tolerance:"pointer",accept:".product",over:function(e,t){$(this).addClass("active")},out:function(e,t){$(this).removeClass("active")},deactivate:function(e,t){$(this).removeClass("active")},drop:function(e,t){$categoryId=$(this).data("categoryid"),$product=$(t.draggable),$.post("/product/"+$product.data("productid")+"/change/category/"+$categoryId,{},function(){$product.remove()}),$(this).removeClass("active")}}),$(".sticker_preview_div .sticker").draggable({containment:"parent",create:function(){stikcer_left=0,sticker_right=0},stop:function(){sticker_left=$(this).position().left,sticker_top=$(this).position().top}}),$(".sticker_preview_div .sticker").resizable({containment:"parent",create:function(){sticker_width=$(this).width(),sticker_height=$(this).height()},stop:function(){sticker_width=$(this).width(),sticker_height=$(this).height()}});