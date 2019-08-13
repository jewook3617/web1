<?php

require_once("top_view.php");



// If the session vars aren't set, try to set them with a cookie
if (!isset($_SESSION['user_data']))
{
	if (isset($_COOKIE['user_data']))
	{
		$_SESSION['user_data'] = $_COOKIE['user_data'];
	}
}

if (isset($_SESSION['user_data']))
{
	//echo "logining";
	$login = true;
}
else
{
	$login = false;
	echo '<script>alert("로그인 후 시도해주세요.");location.href="login.php";</script>';
	exit;
}

?>


		<?php
		if ($_SERVER['REQUEST_METHOD'] == 'GET'){
			$p_id = $_GET['p_id'];

			$conn = mysqli_connect($servername,$username,$password_db, $dbname);

			$result = mysqli_query($conn, "SELECT p_id, s_id, name, category1, category2, price, measure, start_time, content, img_dir FROM product where p_id = $p_id");

			if ($result->num_rows == 1){
		    $row = $result->fetch_assoc();

				$p_id = $row['p_id'];
				$s_id = $row['s_id'];
				$name = $row['name'];
				$category1 = $row['category1'];
				$category2;
				$price = $row['price'];
				$measure = $row['measure'];
				$start_time = date('Y-m-d');
				$content = $row['content'];
				$img_dir = $row['img_dir'];

				$count = 1;
			}
		}
		?>
 <form enctype="multipart/form-data" name="input" method="POST">
 <div id="join-form">
 	<div id="tit">
 		<h3 align="center">상품정보수정</h3>
 	</div>
		<div class="section-body">
			<div class="table1" align="center">
		    <table style="width:30%" >
					<colgroup>
						<col style="width:72px">
        		<col>
          	<col style="width:100px">
	        </colgroup>
	        <tbody style="float : left">
		        <tr>
	            <th scope="col" class="no">상품명	</th>
							<td>
								<input id="name" class="txtfield" name="rename" value="<?php echo $name; ?>" type="text"/>
							</td>
	          </tr>
						<tr>
	          	<th scope="col" class="no">분류	</th>
							<td>
								<select name="recategory1" value="<?php echo $category1; ?>">
									<option>분류없음</option>
									<option>kimch</option>
								</select><br/>
							</td>
						</tr>
						<tr>
	           	<th scope="col" class="no">판매가	</th>
							<td>
								<input id="price" class="txtfield" name="reprice" value="<?php echo $price; ?>" type="text"  />
							</td>
		        </tr>
					  <tr>
	          	<th scope="col" class="no">단위</th>
							<td>
								<input id="measure" class="txtfield" name="remeasure" value="<?php echo $measure; ?>" type="text"/>
							</td>
		         </tr>
						 <tr>
	             <th scope="col" class="no">상품설명	</th>
					  	 <td>
								 <input id="content" class="txtfield" name="recontent" value="<?php echo $content; ?>" type="text"/>
		  				 </td>
		         </tr>
					   <tr>
	             <th scope="col" class="no">사진	</th>
							 <td>
								<input type="file" name="userfile" value="000">
								<input type="hidden" name="filedir" value="<?php echo $img_dir?>"/>
						 	 </td>
		          </tr>
	           </tbody>
		       </table>
			    </div>
					<div class="btn" align="center">
						<input type="submit" style="width: 100px;height: 30px;" value=" 상품수정완료 " />
						<button type="button" class="cancel"><a href="./index.php">취소</a></button>
					</div>
				</div>
			</div>
		</form>
		<div id="footer">
			<img src="./img_main/footer.png" style="width:100%">
		</div>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$p_id = $_GET['p_id'];

  $name = $_POST['rename'];
	$category1 = $_POST['recategory1'];
  $price = $_POST['reprice'];
  $measure = $_POST['remeasure'];
  $start_time = date('Y-m-d');
  $content = $_POST['recontent'];

  ini_set("display_errors", "1");
  $uploaddir = 'C:\xampp\htdocs\project\img_goods\\';
  $uploadfile = $uploaddir.basename($_FILES['userfile']['name']);
  echo '<pre>';

  $img_dir = $_FILES['userfile']['name'];

	if(empty($name)||empty($category1)||empty($price)||empty($measure)||empty($img_dir)){
    echo '<script>alert("빈칸을 모두 채워주세요.");history.back(-1);</script>';
  }
  else
	{   //유효성 가격 : 숫자인지 확인
    if(!is_numeric($price)){
      echo '<script>alert("가격은 숫자만 써주세요.");history.back(-1);</script>';
    }// 문자열 변수에 숫자이외의 문자가 포함되어 있으면
    else{


	$conn = mysqli_connect($servername,$username,$password_db, $dbname);

	$result = mysqli_query($conn, "UPDATE  product
					SET name='$name', category1='$category1', category2='null',price='$price', measure='$measure', content='$content', img_dir='$img_dir'
					WHERE p_id ='$p_id'");


	// query가 정상실행 되었다면,
	if($result)
	{
		// 자동 카운트된 숫자를 $no로 저장
		 //$no = $conn->update_id;
		// 작성된 게시물로 바로 가려면 이동할 URL 설정 ( 아님)
		//$replaceURL = './product_list.php';

		echo '<script>alert("정상적으로 글이 등록되었습니다.");';

		//header('Location: product_list.php');
	}
	else
	{
		echo '<script>alert("등록에 실패했습니다.");</script>';
		exit;
	}
//history.back(-1);
	// Close connection
	$conn->close();
 }
}
}
?>

<div id="footer">
	<img src="./img_main/bottom.png" alt="">

</div>
<div id="footer">
		<img src="./img_main/footer.png" alt="">
</div>
			</body>
		</html>
