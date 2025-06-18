# 프로젝트 파일에서 특정 디렉토리 제외

불필요한 디렉토리를 제외하기

`phpstorm-wp-sample.iml` 파일에 저장. 이 파일도 XML 형식임은 동일

`module > component["NewModuleRootManager"] > content[url="file://$MODULE_DIR$"]` 에 저장

---
저장 전

```xml
<?xml version="1.0" encoding="UTF-8"?>
<module type="WEB_MODULE" version="4">
  <component name="NewModuleRootManager">
    <content url="file://$MODULE_DIR$">
      <sourceFolder url="file://$MODULE_DIR$/wp-includes" isTestSource="false" packagePrefix="PHPMailer" />
    </content>
    <orderEntry type="inheritedJdk" />
    <orderEntry type="sourceFolder" forTests="false" />
  </component>
</module>
```


---
저장 후

```xml
<?xml version="1.0" encoding="UTF-8"?>
<module type="WEB_MODULE" version="4">
  <component name="NewModuleRootManager">
    <content url="file://$MODULE_DIR$">
      <sourceFolder url="file://$MODULE_DIR$/wp-includes" isTestSource="false" packagePrefix="PHPMailer" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/plugins/akismet" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/plugins/naran-hide-welcome-guide" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/plugins/naran-persistent-login" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/themes" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/themes/twentytwentyfour" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/themes/twentytwentythree" />
      <excludeFolder url="file://$MODULE_DIR$/wp-content/themes/twentytwentytwo" />
    </content>
    <orderEntry type="inheritedJdk" />
    <orderEntry type="sourceFolder" forTests="false" />
  </component>
</module>
```
