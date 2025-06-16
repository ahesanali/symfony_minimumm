<?php

namespace App\Utils;



/**
* This class will used to display pagination for data list or grid.
*/
class Paginator
{
   private $totalRecords;
   private $totalPages;
   private $currentPage;
   private $pageSize;
   private $pageURL;

   public function __construct($total_records, $page_size, $page_url)
   {
       $this->totalRecords = $total_records;
       $this->pageURL = $page_url;
       if (isset($_GET['page_size'])) {
           $this->changePageSize($_GET['page_size']);
       } else {
           $this->pageSize = is_int($page_size) ? $page_size : 20;
       }

       $this->calculateTotalPages($this->pageSize);

        //echo "creating paginator real object";
   }
    /**
     * Static method to create pagintor object.
     *
     * @param int    $total_records
     * @param int    $page_size
     * @param string $page_url
     *
     * @return Paginator
     */
    public static function createPagination($total_records, $page_size, $page_url)
    {
        static $paginator_instance;
        if (is_null($paginator_instance)) {
            $paginator_instance = new self($total_records, $page_size, $page_url);
        }

        return $paginator_instance;
    }

    /**
     * Change Pagesize.
     *
     * @param [type] $page_size [description]
     *
     * @return [type] [description]
     */
    public function calculateTotalPages($page_size)
    {
        $this->totalPages = ceil($this->totalRecords / $this->pageSize);
    }

    /**
     * Change page size.
     *
     * @param int $new_page_size
     */
    public function changePageSize($new_page_size)
    {
        $this->pageSize = $new_page_size;
    }

    /**
     * Get Current Page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $this->currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        return $this->currentPage;
    }

    /**
     * Get Page Size.
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Display Paginator Component.
     *
     * @return HTMLString
     */
    public function renderPagnationLinks()
    {
        global $app_name;

        $this->currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        echo '<script type="text/javascript">
          function changePageSize(page_url,current_page)
          {
            var page_size = document.getElementById("per_page").value;
            window.location=page_url+"?page="+current_page+"&page_size="+page_size;
          }
        </script>';
        echo '<div style="display:inline-block;padding-right:20px"><span>Per Page:</span>
        <select id="per_page" name="per_page" onChange="changePageSize(\''.$this->pageURL.'\','.$this->currentPage.')">
          <option>Per Page</option>
          <option '.($this->pageSize == 20 ? 'selected' : '').'>20</option>
          <option '.($this->pageSize == 50 ? 'selected' : '').'>50</option>
          <option '.($this->pageSize == 100 ? 'selected' : '').'>100</option>
        </select></div>';
        //Previous Page Link
        if ($this->currentPage >  1) {
            echo '<a href="'.$this->pageURL.'?page='.($this->currentPage - 1).'&page_size='.$this->pageSize.'"><img src="'.$app_name.'/Images/left.png" alt="Prev"/></a> ';
        }

        //Showing pages high;ight
        echo $this->currentPage.' of '.$this->totalPages.' Pages';

        //Next Page Link
        if ($this->currentPage < $this->totalPages) {
            echo ' <a href="'.$this->pageURL.'?page='.($this->currentPage + 1).'&page_size='.$this->pageSize.'"><img src="'.$app_name.'/Images/right.png" alt="Next"/></a>';
        }
    }
}
