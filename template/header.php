<!DOCTYPE html>
<html lang="en">

<head>
	<?php
	$offset = 60 * 15;
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
	header("Cache-Control: max-age=$offset, must-revalidate");
	?>
    <base href="<?php echo $_url; ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<title><?= $_title; ?></title>
	
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
	<link rel="stylesheet" href="template/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="template/assets/css/mdb.css">
	<link rel="stylesheet" href="template/assets/css/style.css">
	<link rel="stylesheet" href="template/assets/css/tablesort.css">

	<!-- JQuery -->
	<script src="template/assets/js/vue.js"></script>
	<script src="template/assets/js/axios.min.js"></script>
	<script src="template/assets/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="template/assets/js/Chart.min.js"></script>
	<script type="text/javascript">
		function loadOrderBy(url) {
			$(".sort-heading").click(function() {
				var getSortHeading = $(this);
				var getNextSortOrder = getSortHeading.attr('id');
				var splitID = getNextSortOrder.split('-');
				var splitIDName = splitID[0];
				var splitOrder = splitID[1];
				var getColumnName = getSortHeading.text();
				$.ajax({
					url: url,
					type: 'post',
					data: {
						'column': splitIDName,
						'sortOrder': splitOrder
					},
					success: function(response) {
						if (splitOrder == 'asc') {
							getSortHeading.attr('id', splitIDName + '-desc');
							getSortHeading.addClass('table_desc');
							getSortHeading.removeClass('table_asc');
						} else {
							getSortHeading.attr('id', splitIDName + '-asc');
							getSortHeading.removeClass('table_desc');
							getSortHeading.addClass('table_asc');
						}

						$(".table tr:not(:first)").remove();
						$(".table").append(response);
					}
				});
			});
		}
		function copyToClipboard(element){
			var range, selection, worked;

			if (document.body.createTextRange) {
				range = document.body.createTextRange();
				range.moveToElementText(element);
				range.select();
			} else if (window.getSelection) {
				selection = window.getSelection();        
				range = document.createRange();
				range.selectNodeContents(element);
				selection.removeAllRanges();
				selection.addRange(range);
			}
			
			try {
				document.execCommand('copy');
				alert('Ukey copied!');
			}
			catch (err) {
				alert('Browser unable to copy text!');
			}
		}
	</script>
</head>

<body class="fixed-sn black-skin">
	<header>
		<!-- Sidebar navigation -->
		<div id="slide-out" class="side-nav fixed">
			<ul class="custom-scrollbar">
				<!-- Logo -->
				<li class="logo-sn waves-effect py-3" style="border-bottom: 0.1px solid #383838;">
					<div class="text-center logo" style="margin-top:-8px;padding-bottom:15px;">
						<a href="index.php" class="pl-0">
							CALL<span>CENTER</span>
						</a>
					</div>
					<div class="account-type">
						Account Type: <span class="text-danger"><b><?= getTypeAccount(); ?></b></span></br>
						Ukey: <b><span class="text-danger copy-to-clipboard" title="Click to copy user key" onclick="copyToClipboard(this)"><?= getUkey(); ?></span></b>
					</div>
					<p></p>
					<?php if (isCaller() || isColler()) { ?>
						<div class="account-type">
							Chiết khấu: <b class="text-info">+<?= addDotNumber($_user['group_payout']); ?></b>k / sản phẩm</span>
						</div>
						<div class="account-type">
							Khấu trừ: <b class="text-info">-<?= addDotNumber($_user['group_deduct']); ?></b>k / đơn lỗi</span>
						</div>
					<?php } ?>
				</li>
				<li>
					<ul class="collapsible collapsible-accordion">
						<li role="statistics">
							<a class="collapsible-header waves-effect"><i class="w-fa fas fa-chart-line"></i>Statistics <i class="fas fa-angle-down rotate-icon"></i></a>
							<div class="collapsible-body">
								<ul>
									<li data-item="statisticsAds">
										<a href="?route=statistics" class="waves-effect">Statistics Ads</a>
									</li>
									<?php if (isAdmin()) { ?>
										<?php /*
                                        <li role="statistics2">
											<a href="?route=statistics&coll=true" class="waves-effect">Statistics Collaborator</a>
										</li> */
                                        ?>
										<li role="statistics-postback">
											<a href="?route=statistics-postback" class="waves-effect">Statistics Postback</a>
										</li>
										<li role="statistics-province">
											<a href="?route=statistics-province" class="waves-effect">Statistics Province</a>
										</li>

									<?php } ?>
								</ul>
							</div>
						</li>
						<?php if (isAdmin() || isPublisher()) { ?>

							<li role="landing">
								<a href="?route=landing" class="collapsible-header waves-effect"><i class="w-fa fas fa-project-diagram"></i>Landing Stats</a>
							</li>
						<?php } ?>
						<?php if (isCaller()) { ?>
							<li role="newOrder">
								<a href="?route=newOrder" class="collapsible-header waves-effect"><i class="w-fa fas fa-shopping-cart"></i>New Orders<?= ($_count_uncheck > 0 ? '<span class="badge orange" style="position: absolute;top: 10px;right: 20px">' . $_count_uncheck . '</span>' : ''); ?></a>
							</li>
							<li role="calling">
								<a href="?route=calling" class="collapsible-header waves-effect"><i class="w-fa fas fa-phone-volume"></i>My Calling<?= ($_count_calling ? '<span class="badge orange" style="position: absolute;top: 10px;right: 20px">' . $_count_calling . '</span>' : ''); ?></a>
							</li>
							<?php if (isLeader() || isAller()) { ?>
								<li role="allCalling">
									<a href="?route=allCalling" class="collapsible-header waves-effect"><i class="w-fa fas fa-headset"></i>Member's Call<?= ($_count_allcalling ? '<span class="badge orange" style="position: absolute;top: 10px;right: 20px">' . $_count_allcalling . '</span>' : ''); ?></a>
								</li>
							<?php } ?>
						<?php } ?>
						<?php if (isShipper()) { ?>
							<li role="newPending">
								<a href="?route=newPending" class="collapsible-header waves-effect"><i class="w-fa fas fa-hourglass-half"></i>New Pending<?= ($_count_newpending > 0 ? '<span class="badge orange" style="position: absolute;top: 10px;right: 20px">' . $_count_newpending . '</span>' : ''); ?></a>
							</li>

							<li role="newDelay">
								<a href="?route=newDelay" class="collapsible-header waves-effect"><i class="w-fa fas fa-calendar-check"></i>New ShipDelay<?= ($_count_newshipdelay > 0 ? '<span class="badge orange" style="position: absolute;top: 10px;right: 20px">' . $_count_newshipdelay . '</span>' : ''); ?></a>
							</li>

						<?php } ?>
						<?php if (isColler() || isPublisher()) { ?>
							<li role="addOrder">
								<a href="?route=addOrder" class="collapsible-header waves-effect"><i class="w-fa fas fa-cart-plus"></i>Add Order</a>
							</li>
						<?php } ?>
						<li role="order">
							<a class="collapsible-header waves-effect arrow-r">
								<i class="w-fa fas fa-tasks"></i>Quản lí đơn hàng<i class="fas fa-angle-down rotate-icon"></i>
							</a>
							<div class="collapsible-body">
								<ul>
									<?php if (isShipper() || isAdmin()) { ?>
										<li data-item="importOrder">
											<a href="?route=importOrder" class="waves-effect">Import Order</a>
										</li>
										<li data-item="crawlVnpost">
											<a href="?route=crawlVnpost" class="waves-effect">Crawl VNPost Data</a>
										</li>
									<?php } ?>
									<?php if (isCaller() || isPublisher()) { ?>
										<!--
											<li data-item="callback">
											<a href="?route=order&type=callBack" class="waves-effect">Hẹn gọi lại  (<?= '<span class="text-warning">' . $_count_callback . '</span>'; ?>)</a>
											</li>
										-->
										<li data-item="callerror">
											<a href="?route=order&type=callError" class="waves-effect">Không gọi được (<?= '<span class="text-danger">' . $_count_callerror . '</span>'; ?>)</a>
										</li>
									<?php } ?>
									<li data-item="pending">
										<a href="?route=order&type=pending" class="waves-effect">Chuẩn bị giao (<?= '<span class="text-warning">' . $_count_pending . '</span>'; ?>)</a>
									</li>
									<li data-item="shipping">
										<a href="?route=order&type=shipping" class="waves-effect">Đang giao hàng (<?= '<span class="text-info">' . $_count_shipping . '</span>'; ?>)</a>
									</li>
									<li data-item="approved">
										<a href="?route=order&type=shipfail" class="waves-effect">Phát chưa thành công (<?= '<span class="text-info">' . $_count_shipping_fail . '</span>'; ?>)</a>
									</li>

									<li data-item="shipdelay">
										<a href="?route=order&type=shipDelay" class="waves-effect">Hẹn giao hàng <?= '(<span class="text-danger">' . $_count_shipdelay . '</span>)' ?></a>
									</li>
									<li data-item="shiperror">
										<a href="?route=order&type=shipError" class="waves-effect">Không nhận hàng (<?= '<span class="text-warning">' . $_count_shiperror . '</span>'; ?>)</a>
									</li>
									<?php if (isCaller() || isPublisher()) { ?>
										<li data-item="rejected">
											<a href="?route=order&type=rejected" class="waves-effect">Từ chối mua (<?= '<span class="text-warning">' . $_count_rejected . '</span>'; ?>)</a>
										</li>
										<li data-item="trashed">
											<a href="?route=order&type=trashed" class="waves-effect">Đơn hàng rác (<?= '<span class="text-warning">' . $_count_trashed . '</span>'; ?>)</a>
										</li>
									<?php } ?>
									<li data-item="approved">
										<a href="?route=order&type=approved" class="waves-effect">Giao hàng thành công (<?= '<span class="text-warning">' . $_count_approved . '</span>'; ?>)</a>
									</li>
								</ul>
							</div>
						</li>
						<?php if (isPublisher()): ?>
							<li role="postback">
								<a class="collapsible-header waves-effect arrow-r">
									<i class="w-fa fas fa-tasks"></i>Postback logs<i class="fas fa-angle-down rotate-icon"></i>
								</a>
								<div class="collapsible-body">
									<ul>
										<li data-item="postback">
											<a href="?route=postback&type=success" class="waves-effect">Postback Success (<?= '<span class="text-info">' . $_count_postback_success . '</span>'; ?>)</a>
										</li>
										<li data-item="postback">
											<a href="?route=postback&type=fail" class="waves-effect">Postback Fail (<?= '<span class="text-warning">' . $_count_postback_fail . '</span>'; ?>)</a>
										</li>
									</ul>
								</div>
							</li>
                        <?php endif; ?>
						<?php /* if (isAdmin()) { ?>
							<li role="category">
								<a class="collapsible-header waves-effect arrow-r">
									<i class="w-fa fas fa-tasks"></i>Quản lý người dùng<i class="fas fa-angle-down rotate-icon"></i>
								</a>
								<div class="collapsible-body">
									<ul>
										<li data-item="category" style="background:#000;margin-top:5px;">
											<a style="color:#fff !important;"><i class="w-fa fas fa-tasks"></i>Category</a>
										</li>
										<li data-item="commentCategoryOne">
											<a href="?route=commentCategoryOne">Comment Category 1 (<?php echo '<span class="text-info">' . $_count_category_1 . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=commentCategoryTwo">Comment Category 2 (<?php echo '<span class="text-info">' . $_count_category_2 . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=commentLanding">Landing Page (<?php echo '<span class="text-info">' . $_count_comment_landing . '</span>'; ?>)</a>
										</li>
										<li data-item="category" style="background:#000;">
											<a style="color:#fff !important;"><i style="color:#fff;" class="far fa-envelope"></i>Email Marketing</a>
										</li>
										<li data-item="category">
											<a href="?route=addEmailMarketing&action=add">Add Email Marketing</a>
										</li>
										<li data-item="category">
											<a href="?route=emailMarketing">New Email Marketing (<?php echo '<span class="text-info">' . $_count_email_new . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=emailMarketing&type=accept">Chấp nhận Email (<?php echo '<span class="text-info">' . $_count_email_new1 . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=emailMarketing&type=refuse">Từ chối Email (<?php echo '<span class="text-danger">' . $_count_email_new2 . '</span>'; ?>)</a>
										</li>
										<li data-item="category" style="background:#000;">
											<a style="color:#fff !important;"><i style="color:#fff;" class="far fa-comment-dots"></i>Comments</a>
										</li>
										<li data-item="category">
											<a href="?route=addComment">Add Comments </a>
										</li>
										<li data-item="category">
											<a href="?route=comment">New Comments (<?php echo '<span class="text-info">' . $_count_comments . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=comment&type=accept">Chấp nhận Comments (<?php echo '<span class="text-info">' . $_count_comment_accept . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=comment&type=refuse">Từ chối Comments (<?php echo '<span class="text-danger">' . $_count_comment_noaccept . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=addCommentReply&action=add">Add Comment Reply </a>
										</li>
										<li data-item="category">
											<a href="?route=commentReply&type=uncheck">Trả lời Comment Mới (<?php echo '<span class="text-info">' . $_count_comment_reply . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=commentReply&type=accept">Chấp nhận Trả lời (<?php echo '<span class="text-info">' . $_count_comment_reply1 . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=commentReply&type=refuse">Từ chối Trả lời (<?php echo '<span class="text-danger">' . $_count_comment_reply2 . '</span>'; ?>)</a>
										</li>
										<li data-item="category">
											<a href="?route=commentBad">Báo xấu (<?php echo '<span class="text-danger">' . $_count_comment_bad . '</span>'; ?>)</a>
										</li>
									</ul>
								</div>
							</li>
						<?php
						} */
						?>
						<?php if ($_user['group'] && isLeader()) { ?>
							<li role="group">
								<a class="collapsible-header waves-effect arrow-r">
									<i class="w-fa fas fa-users"></i>Group<i class="fas fa-angle-down rotate-icon"></i>
								</a>
								<div class="collapsible-body">
									<ul>
										<li data-item="groupStats">
											<a href="?route=statistics&nav=groupStats&group=<?= $_user['group']; ?>" class="waves-effect">Statistics</a>
										</li>
										<li data-item="members">
											<a href="?route=group" class="waves-effect">Members</a>
										</li>
										<?php if (isLeader() && (isCaller() || isColler())) { ?>
											<li data-item="payment">
												<a href="?route=groupPayment" class="waves-effect">Payments</a>
											</li>
										<?php } ?>
									</ul>
								</div>
							</li>
						<?php } ?>
						<?php if (isAdmin()) { ?>
							<li role="settings">
								<a class="collapsible-header waves-effect arrow-r">
									<i class="w-fa fas fa-cogs"></i>Admin Settings<i class="fas fa-angle-down rotate-icon"></i>
								</a>
								<div class="collapsible-body">
									<ul>
										<li data-item="backlist">
											<a href="?route=settings&type=backlist" class="waves-effect">Backlist Management</a>
										</li>
										<li data-item="offers">
											<a href="?route=settings&type=offers" class="waves-effect">Offers Management</a>
										</li>
										<li data-item="ads">
											<a href="?route=settings&type=ads" class="waves-effect">Ads Management</a>
										</li>
										<li data-item="groups">
											<a href="?route=settings&type=groups" class="waves-effect">Groups Management</a>
										</li>
										<li data-item="payment">
											<a href="?route=settings&type=payment" class="waves-effect">Payment Management</a>
										</li>
									</ul>
								</div>
							</li>
						<?php } ?>

						<!-- Simple link -->
						<?php if (isCaller() || isAdmin()) { ?>
							<li role="payments">
								<a href="?route=payments" class="collapsible-header waves-effect"><i class="w-fa fas fa-money-bill-alt"></i>Payments History</a>
							</li>
						<?php } ?>
						<li role="profile">
							<a href="?route=profile" class="collapsible-header waves-effect"><i class="w-fa fas fa-user"></i>Account Settings</a>
						</li>
					</ul>
				</li>
				<!-- Side navigation links -->
			</ul>
			<div class="sidenav-bg mask-strong"></div>
		</div>
		<!-- Sidebar navigation -->

		<!-- Navbar -->
		<nav class="navbar fixed-top navbar-expand-lg scrolling-navbar double-nav">
			<!-- SideNav slide-out button -->
			<div class="float-left">
				<a href="#" data-activates="slide-out" class="button-collapse"><i class="fas fa-bars"></i></a>
			</div>
			<!-- Breadcrumb -->
			<div class="breadcrumb-dn mr-auto">
				<div class="header-price">
					<div class="price-1">
						Earning:
						<b><?= addDotNumber($_user['revenue_approve']); ?></b>
					</div>
					<div class="price-2">
						Hold:
						<b><?= addDotNumber($_user['revenue_pending']); ?></b>
					</div>
					<div class="price-3">
						Deduct:
						<b><?= addDotNumber($_user['revenue_deduct']); ?></b>
					</div>
				</div>
			</div>
			<!-- Navbar links -->
			<ul class="nav navbar-nav nav-flex-icons ml-auto">
				<!-- Dropdown -->
				<li class="nav-item dropdown notifications-nav">
					<a class="nav-link dropdown-toggle waves-effect" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="badge blue"><?php echo count($_notifications); ?></span>
						<i class="fas fa-bell"></i>
						<span class="d-none d-md-inline-block">Notifications</span>
					</a>
					<div class="dropdown-menu dropdown-primary" aria-labelledby="navbarDropdownMenuLink">
						<?php if (count($_notifications) > 0) {
							$i = 0;
							foreach ($_notifications as $noti) {
								if ($i > 5) {
									echo '<a class="dropdown-item" href="?route=notifications">
										<span>Xem tất cả thông báo...</span>
		            				</a>';
									break;
								} else {
									echo '<a class="dropdown-item" href="?route=notifications&id=' . $noti['id'] . '">
						              <span>' . $noti['title'] . '</span>
						              <span class="float-right"><i class="far fa-clock" aria-hidden="true"></i> ' . get_time($noti['time']) . '</span>
						            </a>';
								}
								$i++;
							}
						} else { ?>
							<a class="dropdown-item" href="?route=notifications">
								<span>Xem tất cả thông báo...</span>
							</a>
						<?php } ?>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle waves-effect" href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<img class="rounded-circle mini-avatar" src="<?= getAvatar($_user['id']); ?>" width="32" heigth="32"> <span class="clearfix inline-block"><?= _e($_user['name']); ?></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
						<a class="dropdown-item" href="?route=profile"><i class="w-fa fas fa-sign-out-alt"></i> Thông tin cá nhân</a>
						<a class="dropdown-item" href="?route=signout"><i class="w-fa fas fa-sign-out-alt"></i> Đăng xuất</a>
					</div>
				</li>
			</ul>
			<!-- Navbar links -->
		</nav>
		<!-- Navbar -->
	</header>

	<main>
		<div class="container-fluid">