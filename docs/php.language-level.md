# PHP 언어 레벨 조정

정적 문법 체크를 위한 PHP 언어 레벨 조정 설정

`.idea/php.xml` 파일

&lt;project version="4"&gt; 루트 엘리먼트 아래 &lt;component name="PhpProjectSharedConfiguration"&gt;
엘리먼트에 저장됨.

--- 
세팅 전
```xml
  <component name="PhpProjectSharedConfiguration" php_language_level="8.0">
    <option name="suggestChangeDefaultLanguageLevel" value="false" />
  </component>
```

---
세팅 후
```xml
  <component name="PhpProjectSharedConfiguration" php_language_level="7.4">
    <option name="suggestChangeDefaultLanguageLevel" value="false" />
  </component>
```