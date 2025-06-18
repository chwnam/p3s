# Composer.json 설정

IDE에서 제대로 워드프레스 플러그인이나 테마의 네임스페이스를 인식하고 지원하기 위해, 기타 composer.json 설정을 
IDE에서 제대로 활용하기 위해 정확한 composer.json 경로를 지정하는 설정이 필요하다.

`workspace.xml` 파일에 저장.

`project > component[name="ComposerSettings"]` 

- composer 실행 경로는 그냥 전역에서 가능한 composer.
  - 절대 경로로 입력할 수 있음


---
저장 전

```xml
  <component name="ComposerSettings">
    <execution />
  </component>
```


---
저장 후

```xml
  <component name="ComposerSettings" synchronizationState="SYNCHRONIZE">
    <pharConfigPath>$PROJECT_DIR$/wp-content/plugins/sample/composer.json</pharConfigPath>
    <execution>
        <executable path="composer" />
    </execution>
  </component>
```

---
 