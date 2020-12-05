
## Documentation

#### Search for a User and get only the ids

````
$ids = User::searchByQuery([
    'multi_match' => [
        'query' => $query,
        'type' => 'bool_prefix',
        'fields' => ['first_name', 'last_name']
    ]
])->pluck('id');
````
