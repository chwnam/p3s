# 커스텀 사전 추가

커스텀 단어를 추가하면 다음과 같은 변화가 생긴다.

`.idea/workspace.xml` 파일, 
&lt;project&gt; 루트 노드 아래 &lt;component name="SpellCheckerSettings"&gt; 
엘리먼트에 저장됨.

--- 
세팅 전
```xml
<component
 name="SpellCheckerSettings"
 RuntimeDictionaries="0"
 Folders="0"
 CustomDictionaries="0"
 DefaultDictionary="application-level"
 UseSingleDictionary="true"
 transferred="true"
/>
```

---
세팅 후
```xml
<component
 name="SpellCheckerSettings"
 RuntimeDictionaries="0"
 
 Folders="3"
 Folder0="$PROJECT_DIR$/../libs/ko-aff-dic-0.7.94"
 Folder1="$PROJECT_DIR$/../libs/custom-plugin-directory"
 Folder2="$PROJECT_DIR$/wp-content/plugins/sample"
 
 CustomDictionaries="3"
 CustomDictionary0="$PROJECT_DIR$/../libs/ko-aff-dic-0.7.94/ko.dic"
 CustomDictionary1="$PROJECT_DIR$/../libs/custom-plugin-directory/custom.dic"
 CustomDictionary2="$PROJECT_DIR$/wp-content/plugins/sample/custom.dic"
 
 DefaultDictionary="application-level"
 UseSingleDictionary="true"
 transferred="true"
/>
```
