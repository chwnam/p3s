# 버전 컨트롤 설정 조정

git 리포지터리 설정

`.idea/vcs.xml` 파일

```xml
<project version="4">
    <component name="VcsDirectoryMappings">
        <mapping directory="" vcs=""> <!-- 여기 ! -->
```


--- 
세팅 전
```xml
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
    <component name="VcsDirectoryMappings">
        <mapping directory="" vcs="Git" />
    </component>
</project>
```

---
세팅 후
```xml
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
    <component name="VcsDirectoryMappings">
        <mapping directory="" vcs="Git" />
        <mapping directory="$PROJECT_DIR$/wp-content/plugins/sample" vcs="Git" />
    </component>
</project>
```
