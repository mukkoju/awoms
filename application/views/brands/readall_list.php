<?php
// Create Button
if ($showCreateButton)
{
    ?>
    <button type='button' class="button_save" onClick="location.href = '/<?php echo $this->controller . "/create"; ?>';">
        Add <?php echo $label; ?>
    </button>
    <?php
}

// No Items?
if (empty($items))
{
    echo "<div class='alert warning'>Sorry! There are no " . $label . "s yet.</div>";
    return;
}
?>

<table class="bordered">
    <tr>
        <th>Edit</th>
        <th>FavIcon</th>
        <?php
        if ($showBrandColumn)
        {
            echo "<th>Brand Name</th>";
        }
        ?>
        <th><?php if (empty($this->model))
        {
            echo trim(ucfirst($this->controller), "s");
        }
        else
        {
            echo $this->model;
        } ?> Name</th>
    </tr>

    <?php
    // Rows
    $lastBrandName = "";
    foreach ($items as $item)
    {
        $itemID = $item[$lbl . 'ID'];
        $itemName = $item[$lbl . 'Name'];
        $itemNameSEO = str_replace(' ', '-', $itemName);
        $itemFavIcon = $item[$lbl . 'FavIcon'];
        $updateLink = BRAND_URL . $this->controller . '/update/' . $itemID . '/' . $itemNameSEO;

//        $activeClass = "success";
//        $activeLabel = "Y";
//        if ($item[$lbl . 'Active'] == 0)
//        {
//            $activeClass = "failure";
//            $activeLabel = "N";
//        }

        $rowData = "<tr>";
        //$rowData .= "<td><div class='alert " . $activeClass . " no-img center'>" . $activeLabel . "</div></td>";

        $rowData .= "<td><a href='" . $updateLink . "'>[Edit]</a></td>";

        if (!empty($itemFavIcon))
        {
            $rowData .= "<td><img src='$itemFavIcon'/></td>";
        }
        else
        {
            $rowData .= "<td>&nbsp;</td>";
        }

        if ($showBrandColumn)
        {
            foreach ($brands as $brand)
            {
                if ($item['brandID'] == $brand['brandID'])
                {
                    if ($lastBrandName != $brand['brandName'])
                    {
                        $rowData .= "<td>" . $brand['brandName'] . "</td>";
                    }
                    else
                    {
                        $rowData .= "<td>&nbsp;</td>";
                    }
                    break;
                }
            }
            $lastBrandName = $brand['brandName'];
        }

        $rowData .= "
            <td>
                <a href='" . $updateLink . "'>
                    " . $itemName . "
                </a>
            </td>
        </tr>";

        echo $rowData;
    }
    ?>
</table>