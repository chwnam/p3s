# 범위 조정

프로젝트 내 특정 범위만 검사하도록 하는 범위(scope)를 지정

`.idea/workspace.xml` 파일,

```
project[version="4"] > component[name="NamedScopeManager"] > scope
``` 

## Scope 엘리먼트

- name 속성: 원하는 이름
- pattern 속성: 텍스트로 지정

---
설정 전

```
<!-- 없음 -->
```

---
설정 후

```xml

<component name="NamedScopeManager">
    <scope
            name="Sample"
            pattern="(!file[phpstorm-wp-sample]:docs//*||file[phpstorm-wp-sample]:wp-content/plugins/sample//*||file:wp-content/plugins/hello.php||file[phpstorm-wp-sample]:wp-admin//*)&amp;&amp;!file[phpstorm-wp-sample]:wp-admin/maint//*"
    />
</component>
```

- 프로젝트 내 경로를 지정할 때 `file[phpstorm-wp-sample]:`로 시작
- 제외하는 경우 `!`를 경로 앞에 붙임
- 열거할 때 포함 항목일 때는 `||` (or) 연산자로 연결
- 열거할 때 제외되는 항목일 때는 `&&` (and) 연산자로 연결 - 이 때 인코딩하여 `&gt;&gt;`로 표시
- 연산자 우선순위를 명시할 때 괄호로 묶을 수 있음
- 한 디렉토리 내 파일을 재귀적으로 포함시킬 경우 `//*`로 지정
