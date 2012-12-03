    <section id="content" class="static">
        
        <article>
            <h1><?php $page->printPageTitle() ?></h1>
            
            <p>Jump to: <a href="#debugging">Debugging</a>, <a href="#queries">Queries</a></p>
            
            <?php $page->printPageContent() ?>
            
            <h2 id="debugging">Debugging: Raw Page Data</h2>
            
            <pre><?php
            $page->processPageTags();
            $pagedata = $page->getPageData();
            echo htmlentities(print_r($pagedata, true));
            ?>
            </pre>
            
            <h2 id="queries">Database Queries for this Request</h2>
            <pre><?php echo implode("\n\n", $page->db->getQueryLog()) ?></pre>
        </article>

    </section><!-- content -->
    
    <?php include 'sidebar.php'; ?>