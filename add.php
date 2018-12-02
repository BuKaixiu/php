<?php 
	function uploadPhoto() {
		// 目标：接收客户端提交的数据和文件，最终保存到数据文件中

		// 接收文件
		global $data;
		$img = array();
		$data = array();		// 准备一个空的容器，用来装最终要保存的数据

		// 1.判断文本域是否存在
		if(empty($_FILES['img'])) {
			$GLOBALS['message'] = '页面出错';
		}

		$images = $_FILES['img'];
		$data['image'] = array();		// 准备了一个容器准备装images地址

		// var_dump($images);
		// 校验
		for($i = 0; $i < count($images['name']); $i++) {
			// 校验文本域
			if ($images['error'][$i] !== UPLOAD_ERR_OK) {
				$GLOBALS['message'] = '校验文本域失败';
				return;
			}
			// 校验类型
			// images['type'] => ['image/png', 'image/jpg', 'image/jpeg']
			if(strpos($images['type'][$i], 'image/') !== 0) {
				$GLOBALS['message'] = '校验类型失败';
				return;
			}
			// 效验大小

			if($images['size'][$i] > 10 * 1024 * 1024) {
				$GLOBALS['message'] = '校验大小失败';
				return;
			}

			// 移动文件到网站范围内

			$dest	 =  './images/' . uniqid() . $images['name'][$i];
			if (!move_uploaded_file($images['tmp_name'][$i], $dest)) {
				$GLOBALS['message'] = '图片移动失败失败';
				return;
			}

			// 保存数据
			$data['image'][] = $dest;
			// var_dump($data['image']);

		}
		$imagesSrc = json_decode(file_get_contents('images.json',true));
		for($i = 0; $i < count($data["image"]); $i ++) {
			$imagesSrc[] = array(
				"image" => $data["image"][$i]
			);
		}
		$json = json_encode($imagesSrc);
		file_put_contents('images.json', $json);
	}
	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		uploadPhoto();
	}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>上传图片</title>
	<link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
	<h1 class="container mt-3">上传图片</h1>
	<?php if (isset($message)): ?>
		<div class="alert alert-danger container btn-sm" role="alert">
  		<?php echo $message ?>
		</div>	
	<?php endif ?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" autocomplete="off" class="container">
		<hr>
		<div class="custom-file">
			<label for="img" class="custom-file-label">上传图片：</label>
			<input type="file" name="img[]" id="img" class="custom-file-input" multiple>
		</div>	
		<button class="btn btn-block btn-primary">提交</button>
	</form>
	<?php foreach ($data as  $value): ?>
		<?php foreach ($value as $value): ?>
			<div class="text-center container">
			<img src="<?php echo $value; ?>" alt="" width="100px" height="100px" class="rounded border border-warning float-left">
			</div>
			
		<?php endforeach ?>
	<?php endforeach ?>
</body>
</html>