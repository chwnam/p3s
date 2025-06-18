# SAOPS (Settings Automation Of PhpStorm)

워드프레스 사용하면서 셋업 매번 하는 게 지겹습니다.
거의 똑같은 사항을 반복합니다. 이런 건 미리 지정해 두고 명령어 한 번에 해결하고 싶습니다.

## 요구사항

- PHP 8.2+

## 설치

```bash
git clone https://github.com/chwnam/saops.git
cd p3s
composer install
composer dump-autoload -o
```

## 셋업

saops.php 파일과 동일한 디렉토리에 `config.json` 파일을 생성합니다.
미리 만들어 둔 `config.json.dist` 예제 파일을 참고하시기 바랍니다.

### config.json 루트

- `version`: `1.0`으로 고정되어 있습니다.
- `target`: 상대경로나 절대경로로 입력합니다. 스크립트를 실행하는 기준이 됩니다.
  예를 들어, 워드프레스를 사용하는 경우 **개발하는 테마나 플러그인의 루트 디렉토리**를 `targetDiectory`으로 설정합니다.
- `projectRoot`: IDE가 기준으로 잡는 프로젝트 루트 디렉토리를 의미합니다. 절대경로 또는 `targetDiectory`의 상대 경로로 입력하세요.
  이 값은 이후 모든 설정에서 상대경로의 기준이 됩니다.
- `setup`: 값을 자동화하려는 항목을 담고 있습니다.

### setup

`setup` 내부에서 설정 가능한 항목입니다.

#### setup.php.languageLevel

IDE에서 문법 검사를 할 때 참조할 PHP language level 값을 지정합니다.

```json
{
  "languageLevel": "8.0"
}
```

#### setup.php.servers

서버 웹 주소를 입력합니다. 목록 안에 들어갈 항목은 host, port를 가진 오브젝트지만
'preset:wordpress' 매직 문자열도 가능합니다.

```json
{
  "servers": [
    {
      "host": "your.domain.com",
      "port": 80
    },
    "preset:wordpress{wp}"
  ]
}
```

매직 문자열 'preset:wordpress'의 역할 및 기능입니다.

- 워드프레스의 로컬 서버 환경을 탐색합니다. 이 때 WP-CLI를 사용합니다.
- {} 안에 WP-CLI 경로를 입력합니다.
- WP-CLI에 대해 PATH가 잡혀 있으면 위 예시처럼 `wp` 명령어만 입력 가능하지만,  
  그렇지 않은 경우 절대 경로를 입력해야 합니다.
- 그러면 `wp option get siteurl`을 사용해 host, port를 자동으로 추철합니다.

#### setup.php.composer

composer.json 경로를 인식시켜 줍니다. PSR-4 네임스페이스나 의존성 패키지 관리에 필요합니다.

현재는 `true`만 지원합니다.
`true`일 때 `targetDiectory`에 있는 composer.json 파일을 설정에 반영해 줍니다.

#### setup.php.frameworks.wordpress

IDE의 워드프레스 지원을 설정합니다.

- `enabled`는 `true`, `false` 입니다.
- `installationPath`는 상대/절대 경로로 입력합니다.

```json
{
  "frameworks": {
    "wordpress": {
      "enabled": true,
      "installationPath": "."
    }
  }
}
```

#### setup.appearanceAndBehavior.scopes

현재 `true`만 지원합니다.

`true`이이고 `targetDiectory`가 `projectDirectory`의 하위 디렉토리일 때
`targetDiectory`를 별도의 scope로 등록해 줍니다.
이름은 `targetDiectory` 디렉토리의 이름을 따릅니다.

#### setup.editor.naturalLanguages.spelling.customDictionaries

철자의 예외 사항을 지정하는 사전의 경로를 지정합니다. 절대경로, 혹은 `targetDiectory`의 상대경로로 입력합니다.
만약 이 설정이 없거나 `false`를 입력하면 이 기능을 사용하지 않습니다.

#### setup.directories.exclusion

현재 `preset:wordpress`만 지원합니다. 이 프리셋은 다음과 같습니다.

- 현재 디렉토리 이외 테마, 플러그인은 프로젝트에서 제외합니다.
- 워드프레스의 직접적인 소스 이외의 디렉토리, 예를 들어 `wp-content/uploads` 같은 경로를 제외합니다.

#### setup.versionControl.directoryMappings

현재 `preset:wordpress`만 지원합니다.

`preset:wordpress`이면 `targetDiectory`만을 VCS 매핑 디렉토리로 만들어 줍니다.

#### setup.runDebugConfiguration.xdebug

현재 `true`만 지원합니다.

`true`이면 'Remote Debugging' 설정을 생성합니다. `server`로 만들어진 항목이 있어야 합니다.
IDE key 값은 `phpstorm-xdebug`로 고정되어 있습니다.

## 실행

아래처럼 스크립트를 실행합니다.
설정에서 `target`이 상대 경로로 되어 있는 경우는 반드시 먼저 해당 디렉토리로 이동해야 합니다.

```
php /path/to/saops.php
```
