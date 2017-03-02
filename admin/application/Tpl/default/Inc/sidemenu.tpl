<aside id="sidebar" class="sidebar c-overflow">
	<ul class="main-menu">

		<?php
			foreach ($_menu as $key => $nav_item) {
				$nav_htm = '';
				$url = isset($nav_item["url"]) ? $nav_item["url"] : "#";
				$icon = isset($nav_item["icon"]) ? '<i class="zmdi zmdi-'.$nav_item["icon"].'"></i>' : "";
				$nav_title = isset($nav_item["title"]) ? $nav_item["title"] : "(没有名字)";
				$nav_htm .= '<a href="'.$url.'" title="'.$nav_title.'">'.$icon.' '.$nav_title.$label_htm.'</a>';

				if (isset($nav_item["children"])) {
					$nav_htm .= process_sub_nav($nav_item["children"]);
					echo '<li '.(isset($nav_item["active"]) ? 'class = "sub-menu active toggled"' : 'class = "sub-menu"').'>'.$nav_htm.'</li>';
				} else {
					echo '<li '.(isset($nav_item["active"]) ? 'class = "active"' : '').'>'.$nav_htm.'</li>';
				}
			}

				function process_sub_nav($nav_item) {
				$sub_item_htm = "";
				$sub_item_htm .= '<ul>';
					foreach ($nav_item as $key => $sub_item) {
					$url = isset($sub_item["url"]) ? $sub_item["url"] : "javascript:;";
					$nav_title = isset($sub_item["title"]) ? $sub_item["title"] : "(No Name)";
					$label_htm = isset($sub_item["label_htm"]) ? $sub_item["label_htm"] : "";
					$sub_item_htm .= '<li><a '.(isset($sub_item["active"]) ? 'class = "active"' : '').' href="'.$url.'">'.$nav_title.$label_htm.'</a></li>';
					}
					$sub_item_htm .= '</ul>';
				return $sub_item_htm;
				}
		?>

	</ul>
</aside>