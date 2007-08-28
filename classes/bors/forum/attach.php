<?
class_include("def_dbpage");

class forum_attach extends def_dbpage
{
	function main_db_storage(){ return 'punbb'; }
	function main_table_storage(){ return 'attach_2_files'; }
				
				
		var $stb_post_id = '';
		function post_id() { return $this->stb_post_id; }
		function set_post_id($post_id, $db_update) { $this->set("post_id", $post_id, $db_update); }
		function field_post_id_storage() { return 'post_id(id)'; }

		var $stb_owner_id = '';
		function owner_id() { return $this->stb_owner_id; }
		function set_owner_id($owner_id, $db_update) { $this->set("owner_id", $owner_id, $db_update); }
		function field_owner_id_storage() { return 'owner(id)'; }

		var $stb_filename = '';
		function filename() { return $this->stb_filename; }
		function set_filename($filename, $db_update) { $this->set("filename", $filename, $db_update); }
		function field_filename_storage() { return 'filename(id)'; }

		function field_title_storage() { return 'filename(id)'; }

		var $stb_extension = '';
		function extension() { return $this->stb_extension; }
		function set_extension($extension, $db_update) { $this->set("extension", $extension, $db_update); }
		function field_extension_storage() { return 'extension(id)'; }

		var $stb_mime = '';
		function mime() { return $this->stb_mime; }
		function set_mime($mime, $db_update) { $this->set("mime", $mime, $db_update); }
		function field_mime_storage() { return 'mime(id)'; }

		var $stb_size = '';
		function size() { return $this->stb_size; }
		function set_size($size, $db_update) { $this->set("size", $size, $db_update); }
		function field_size_storage() { return 'size(id)'; }

		var $stb_downloads = '';
		function downloads() { return $this->stb_downloads; }
		function set_downloads($downloads, $db_update) { $this->set("downloads", $downloads, $db_update); }
		function field_downloads_storage() { return 'downloads(id)'; }

	function url()
	{
		return "http://balancer.ru/forum/punbb/attachment.php?item=".$this->id();
	}
}
