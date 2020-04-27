### Bool Query
The AND/OR/NOT operators can be used to fine tune our search queries in order to provide more 
relevant or specific results. This is implemented in the search API as a bool query. 
The bool query accepts a must parameter (equivalent to AND), 
a must_not parameter (equivalent to NOT), and a should parameter (equivalent to OR). 
For example, if I want to search for a book with the word “Elasticsearch” OR “Solr” 
in the title, AND is authored by “clinton gormley” but NOT authored by “radu gheorge”:

POST /bookdb_index/book/_search
{
  "query": {
    "bool": {
      "must": {
        "bool" : { 
          "should": [
            { "match": { "title": "Elasticsearch" }},
            { "match": { "title": "Solr" }} 
          ],
          "must": { "match": { "authors": "clinton gormely" }} 
        }
      },
      "must_not": { "match": {"authors": "radu gheorge" }}
    }
  }
}
