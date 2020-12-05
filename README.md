
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
````
class Profile extends Model implements ElasticSearchInterface
{
    use ElasticSearchTrait;
    protected $table = 'User';

    protected array $mappingProperties = [
        'first_name' => ['type' => 'text', 'analyzer' => 'standard'],
        'last_name'  => ['type' => 'text', 'analyzer' => 'standard'],
    ];
````

### Manipulate Document

````
class Profile extends Model implements ElasticSearchInterface
{
    use ElasticSearchTrait;
    protected $table = 'User';

    public function addDocument(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'full_name'  => $this->first_name . " " . $this->last_name
        ];
    }
````
