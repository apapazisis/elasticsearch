
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
