
## Documentation

#### Search Users and get only the ids

````
$ids = User::searchByQuery([
    'multi_match' => [
        'query' => $query,
        'type' => 'bool_prefix',
        'fields' => ['first_name', 'last_name']
    ]
])->pluck('id');
````

#### Search Users using sorting and limit, offset
````
$ids = User::searchByQuery([
    'multi_match' => [
        'query' => $query,
        'type' => 'bool_prefix',
        'fields' => ['first_name', 'last_name']
    ]
])
->limit(10)
->offset(5)
->orderBy('first_name.raw', 'desc') <== asc is by default
->pluck('id');
````
#### Set mappings 

class Profile extends Model implements ElasticSearchInterface
{
    use ElasticSearch;
    protected $table = 'User';

    protected array $mappingProperties = [
        'first_name' => ['type' => 'text', 'analyzer' => 'standard'],
        'last_name'  => ['type' => 'text', 'analyzer' => 'standard'],
    ];
