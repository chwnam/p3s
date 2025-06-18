# PHP 서버 목록 조정

정적 문법 체크를 위한 PHP 언어 레벨 조정 설정

`.idea/workspace.xml` 파일

&lt;project version="4"&gt; 루트 엘리먼트 아래 &lt;component name="PhpServers"&gt;
엘리먼트에 저장됨.

--- 
세팅 전
```xml
  <component name="PhpServers">
  </component>
```

---
세팅 후
```xml
  <component name="PhpServers">
    <servers>
        <server host="dummy.localhost" id=".....uuid64...." name="dummy"/>
        <server host="dummy2.localhost" id=".....uuid64...." name="dummy2" port="8443"/>
    </servers>
  </component>
```
