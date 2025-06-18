# XDebug 지원

XDebug 지원되도록 설정하는 것은 항상 반복되는 작업

1. 접속할 워드프레스 도메인을 설정한다
2. 키로 `phpstorm-xdebug`를 지정한다

`workspace.xml` 파일에 저장.

`project > component[name="PhpServers"]`

---
저장 전

```xml

<project version="4">
    <!-- ... -->
    <component name="RunManager">
        <configuration default="true" type="ComposerRunConfigurationType" factoryName="Composer Script">
            <option name="pathToComposerJson" value="$PROJECT_DIR$/wp-content/plugins/sample/composer.json"/>
            <method v="2"/>
        </configuration>
    </component>
    <!-- ... -->
</project>
```

---
저장 후

```xml

<project version="4">
    <!-- ... -->
    <component name="PhpServers">
        <servers>
            <server
                    id="4b7c7f5b-2b3a-4a78-a889-356a05182cbf"
                    host="wordpress.localhost"
                    name="wordpress.localhost"
                    port="8443"
            />
        </servers>
    </component>
    <component name="RunManager">
        <configuration
                name="Remote Debugging"
                type="PhpRemoteDebugRunConfigurationType"
                factoryName="PHP Remote Debug"
                filter_connections="FILTER"
                server_name="wordpress.localhost"
                session_id="phpstorm-xdebug"
        >
            <method v="2"/>
        </configuration>
    </component>
    <!-- ... -->
</project>
```

- component[name="PhpServers"] 에 서버를 새로 추가한다.
- component[name="RunManager"] 에 Configuration 을 추가한다.
