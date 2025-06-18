# Git 디렉토리 매핑

어떤 디렉토리에서 발견되는 git 설정을 추가할지 배제할지 결정한다.

`.idea/vcs.xml`과 `.idea/workspace.xml` 둘 다 이용한다.

`vcs.xml`은 추가할 리포지터리, `workspace.xml`은 무시할 리포지터리를 기록한다.

---

vcs.xml 세팅 예

```xml
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
  <component name="VcsDirectoryMappings">
    <mapping directory="$PROJECT_DIR$" vcs="Git" />
    <mapping directory="$PROJECT_DIR$/wp-content/plugins/naran-disable-heartbeat" vcs="Git" />
  </component>
</project>
```

workspace 세팅 예

```xml
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
    <component name="VcsManagerConfiguration">
        <ignored-roots>
            <path value="$PROJECT_DIR$/wp-content/plugins/naran-hide-welcome-guide" />
            <path value="$PROJECT_DIR$/wp-content/plugins/naran-persistent-login" />
        </ignored-roots>
    </component>
</project>
```