<% if Pages %>
<ul class="c-page-breadcrumbs c-theme-nav c-pull-right c-fonts-regular">
	<% loop Pages %>
		<% if Last %>
        <li class="c-state_active">$Title.XML</li>
        <% else %>
        <li><a href="$Link" title="$MenuTitle.XML">$MenuTitle.XML</a></li>
        <li>/</li>
        <% end_if %>
	<% end_loop %>
</ul>
<% end_if %>
