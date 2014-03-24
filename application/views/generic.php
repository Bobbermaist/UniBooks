
<?php
$attributes = '';

if (isset($class))
    $attributes .= " class=\"$class\"";

if (isset($id))
    $attributes .= " id=\"$id\"";

if (isset($p))
{
    echo "<p$attributes>$p</p>";
}
elseif (isset($div))
{
    echo "<div$attributes>$div</div>";
}
elseif (isset($span))
{
    echo "<span$attributes>$span</span>";
}
