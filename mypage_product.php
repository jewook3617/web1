<?php

require_once("top_view.php");

// Create connection
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Check connection
if ($conn->connect_error)
{
	die("DB Connection failed: " . $conn->connect_error);
}



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

	$user_id = $_SESSION['user_data'];
	$user_id = mysqli_real_escape_string($conn, trim($user_id));

	$sql = 'SELECT * FROM member WHERE id="'.$user_id.'"';
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	// 로그인한 계정의 타입 저장
	$type = $row['mtype'];
}
else
{
	$login = false;

	echo '<script>alert("로그인 후 시도해주세요.");location.href="login.php";</script>';
	exit;
}

if ($type == 'seller')
{
	/* 페이징 시작 */
	//페이지 get 변수가 있다면 받아오고, 없다면 1페이지를 보여준다.
	if (isset($_GET['page']))
	{
		$page = $_GET['page'];
	}
	else
	{
		$page = 1;
	}
	
	$sql = "SELECT count(*) as cnt FROM product WHERE s_id='".$user_id."' ORDER BY p_id DESC";
	//$sql = "SELECT * FROM product WHERE s_id='".$user_id."' ORDER BY p_id DESC";

	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	$allPost = $row['cnt']; //전체 게시글의 수

	$onePage = 15; // 한 페이지에 보여줄 게시글의 수.

	$allPage = ceil($allPost / $onePage); //전체 페이지의 수

	if ($page < 1 || ($allPage && $page > $allPage))
	{
		echo '<script>alert("존재하지않는 페이지입니다.");location.href="index.php";</script>';
		exit;
	}

	$oneSection = 10; //한번에 보여줄 총 페이지 개수(1 ~ 10, 11 ~ 20 ...)

	$currentSection = ceil($page / $oneSection); //현재 섹션
	$allSection = ceil($allPage / $oneSection); //전체 섹션의 수
	$firstPage = ($currentSection * $oneSection) - ($oneSection - 1); //현재 섹션의 처음 페이지

	if ($currentSection == $allSection)
	{
		$lastPage = $allPage; //현재 섹션이 마지막 섹션이라면 $allPage가 마지막 페이지가 된다.
	}
	else
	{
		$lastPage = $currentSection * $oneSection; //현재 섹션의 마지막 페이지
	}

	$prevPage = (($currentSection - 1) * $oneSection); //이전 페이지, 11~20일 때 이전을 누르면 10 페이지로 이동.
	$nextPage = (($currentSection + 1) * $oneSection) - ($oneSection - 1); //다음 페이지, 11~20일 때 다음을 누르면 21 페이지로 이동.

	$paging = '<ul class="pagination">'; // 페이징을 저장할 변수

	//첫 페이지가 아니라면 처음 버튼을 생성
	if ($page != 1)
	{
		$paging .= '<li class="active"><a href="mypage_order.php?page=1">처음</a></li>';
	}

	//첫 섹션이 아니라면 이전 버튼을 생성
	if ($currentSection != 1)
	{
		$paging .= '<li class="active"><a href="mypage_order.php?page=' . $prevPage . '">이전</a></li>';
	}

	for ($i = $firstPage; $i <= $lastPage; $i++)
	{
		if ($i == $page)
		{
			$paging .= '<li class="active">' . $i . '</li>';
		}
		else
		{
			$paging .= '<li class="active"><a href="mypage_order.php?page=' . $i . '">' . $i . '</a></li>';
		}
	}

	//마지막 섹션이 아니라면 다음 버튼을 생성
	if ($currentSection != $allSection)
	{
		$paging .= '<li class="active"><a href="mypage_order.php?page=' . $nextPage . '">다음</a></li>';
	}

	//마지막 페이지가 아니라면 끝 버튼을 생성
	if ($page != $allPage)
	{
		$paging .= '<li class="active"><a href="mypage_order.php?page=' . $allPage . '">끝</a></li>';
	}

	$paging .= '</ul>';

	/* 페이징 끝 */

	$currentLimit = ($onePage * $page) - $onePage; //몇 번째의 글부터 가져오는지
	$sqlLimit = ' limit ' . $currentLimit . ', ' . $onePage; //limit sql 구문

	//원하는 개수만큼 가져온다. (0번째부터 20번째까지
	$sql = "SELECT * FROM product WHERE s_id='".$user_id."' ORDER BY p_id DESC".$sqlLimit;
	//$sql = "SELECT * FROM product WHERE s_id='".$user_id."' ORDER BY p_id DESC";

	$result = $conn->query($sql);
}
else
{
  echo '<script>alert("잘못된 접근입니다.");location.href="index.php";</script>';
	exit;
}

?>


    <form id="joinForm" name="joinForm" action="mypage_product_delete.php" method="get" enctype="multipart/form-data" >
      <div id="join" style="background-color:white;">
        <div class="top">
          <h2>마이페이지</h2>
					<?php
					if ($type == 'seller')
					{
					?>
          <button type="button" class="mp-menu"><a href="mypage_product.php" style="color:black;">등록상품조회</a></button>
					<?php
					}
					?>
          <button type="button" class="mp-menu"><a href="mypage_order.php" style="color:black;">주문배송조회</a></button>
          <button type="button" class="mp-menu"><a href="mypage_personal.php" style="color:black;">개인정보수정</a></button>
        </div>
        <div id="join-form">
    			<div id="tit">
    				<h3>등록상품조회</h3>
    			</div>
    			<div id="form_inner">
    				<div>
    					<table border="0" summary="">
    						<caption></caption>
    						<colgroup>
    							<col style="width:100px;"/>
    							<col style="width:auto;"/>
    						</colgroup>
    						<tbody>
    							<tr>
                    <th scope="row">선택</th>
                    <th scope="row">사진</th>
                    <th scope="row">상품명</th>
                    <th scope="row">등록일</th>
    							</tr>
                  <?php
            			$i = 1;
            			while ($row = $result->fetch_assoc())
            			{
            				$p_id = $row['p_id'];
            				$name = $row['name'];
            				$date = $row['start_time'];
            				$img = $row['img_dir'];
            			?>
            			<tr>
            				<td><input type="checkbox" name="pid<?php echo $i; ?>" value="<?php echo $p_id; ?>"></td>
            				<td><a href="product_data.php?no=<?php echo $p_id; ?>"><img src="<?php echo $img; ?>"></a></td>
            				<td><a href="product_data.php?no=<?php echo $p_id; ?>"><?php echo $name; ?></a></td>
            				<td><?php echo $date; ?></td>
            			</tr>
            			<?php
            				$i = $i + 1;
            			}
            			?>
    						</tbody>
    					</table>
							<div class="paging">
								<?php echo $paging ?>
							</div>
    				</div>
    				<br/>
    				<div class="btn">
    					<input type="submit" class="gaip" value="삭제">
    					<button type="button" class="cancel"><a href="./index.php">취소</a></button>
    				</div>
    			</div>
        </div>
      </div>
    </form>
  </div>

	<div id="footer">
		<img src="./img_main/footer.png" alt="">
	</div>
</body>
</html>
