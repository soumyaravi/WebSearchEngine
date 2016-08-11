# WebSearchEngine

• Crawled the website of USC Viterbi School of Engineering using Crawler4j library with certain constraints. 

• Used Crawler4j for scraping the website and downloading HTML, PDF and Microsoft Word Documents.

• Used Apache Solr to index the downloaded files.

• Used NetworkX library in python to create a graph to calculate the link between the downloaded pages to implement Page rank algorithm

• Made use of HTML5, CSS3, PHP and jQuery to design the user interface where the top 10 results are displayed. The user has the option to view the results using either the default Solr ranking algorithm or using Page Rank algorithm.

• Used Porter’s Stemmer algorithm to remove the stop words and used Peter-Norvig Spell Corrector to suggest correct spellings to the user.

• Also used Apache Tika to parse the downloaded files and retrieve the text for the spelling correction algorithm.
