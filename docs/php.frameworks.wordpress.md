# WordPress 지원

add_action(), add_filter() 등 지원 받으려면 WordPress 지원을 설정해 주는 것이 좋다.

`workspace.xml` 파일에 저장.

`project > component[name="WordPressConfiguration"]`

- 엘리먼트가 없을 수 있음


---
저장 전

```
<!-- 공백 ->
```


---
저장 후

```xml

<component name="WordPressConfiguration" enabled="true">
    <wordpressPath>$PROJECT_DIR$</wordpressPath>
</component>
```
