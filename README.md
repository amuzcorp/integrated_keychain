# 통합 키체인 플러그인
Integrated Keychain

### Description

- 서드파티 애플리케이션에서의 API키, Client키, 시크릿키 등 키 중복을 방지하기 위해 통합 체인 관리자를 제공합니다.
- 각 플러그인은 키체인에 필요한 키를 등록할 수 있고, 키 종류는 공급자(vendor/vid)와 제품명(product/pid)으로 구분합니다.
- 여러 플러그인에서 같은 키를 요청하면 키 값이 한번만 저장되어도 여러 플러그인에서 동시에 활용할 수 있습니다.

### Enviroment
- [XpressEngine3](https://github.com/xpressengine/xpressengine "XE3 Git") 코어 3.0.13 이상이 필요합니다.

### How to Register Key

- XeRegister를 통해 다음과 같이 필요한 키를 요청하고 등록합니다.
```
plugin.php 에서 필요한 키를 등록 합니다.
```

```php
public function boot(){ 
    ...
    ...
$keychain = [
    //앱에서 요청할 고유 키 이름을 지정. 가능한 플러그인 고유의 명칭을 사용하는것을 권장 
    'myplugin_kakao_map_key' => [
        'tab' => '확장필드', // 설정화면에 분류될 섹션
        'group' => '지도', // 설정화면에 분류될 그룹
        'how' => '확장필드 위치 및 지도 필드에 활용됩니다.', // 이 플러그인에서 이 키를 왜 요구하는지 작성합니다.
        'pid' => 'map', // product name
        'vid' => 'kakao', // vendor name
        'type' => 'formText', // uio 유형 등록
        'label' => '카카오 지도 API KEY', // uio 라벨
        'options' => [], // SELECT 또는 RADIO uio를 위한 옵션등록
        'description' => '카카오 지도의 자바스크립트 키를 입력합니다.', // uio 설명
        'ordering' => 500 // 설정화면에서 출력할 순서 (실 이용에 큰 상관은 없습니다.)
    ],
    'other_key' => [
        ... // 반복해서 여러개의 키 등록 가능
    ],
];
//self::getId() 대신 다른 플러그인 이름 등록 가능
\XeRegister::push('integrated_keychain',self::getId(),$keychain);
    ...
    ...
}
```

### Service
- 키체인 서비스를 제공합니다. 다음 명령어를 통해 키체인 서비스에 접근할 수 있습니다.
```php
$keychain = app('amuz.keychain');
```
  
### Method
- 저장된 키를 활용 할 때에는 다음과같이 호출할 수 있습니다.
```php
//키에 저장된 값을 즉시 반환
$value = app('amuz.keychain')->getValueById('myplugin_kakao_map_key');
```
```php
//키의 UniqueKey Array를 반환
$uniqueKey = app('amuz.keychain')->getKeyByID('myplugin_kakao_map_key');
```
```php
//반환된 UniqueKey Array
array:7 [▼
  "_type" => "formText"
  "_args" => array:6 [▼
    "name" => "kakao_map"
    "label" => "카카오 지도 API KEY"
    "description" => "카카오 지도의 자바스크립트 키를 입력합니다."
    "placeholder" => null
    "value" => "aaatest"
    "options" => null
  ]
  "value" => "aaatest"
  "id" => "kakao_map"
  "vid" => "kakao"
  "pid" => "map"
  "requester" => array:1 [▼
    "kakao_map_key" => array:5 [▼
      "id" => "dynamic_field_extend"
      "title" => "다이나믹 필드 Extend"
      "requestId" => "kakao_map_key"
      "requestPlugin" => "dynamic_field_extend"
      "how" => "확장필드 위치 및 지도 필드에 활용됩니다."
    ]
  ]
]
```

## 현재 발견된 문제

- ordering이 무시되는 문제 (중요도 매우 낮음)
