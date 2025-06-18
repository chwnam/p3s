<?php

namespace Chwnam\P3S\Editors;

use Chwnam\P3S\EditManager;

interface Editor
{
    public function __construct(EditManager $manager);

    /**
     * 설정을 진행함
     */
    public function edit(): void;

    /**
     * config.json 에 설정된 값을 해석해 가져옴
     */
    public function getConfigSetup(): array;

    /**
     * 변경하려는 설정이 파일 이름을 리턴
     *
     * @return string
     */
    public function getDefaultFileName(): string;

    /**
     * config.json 에서 정의된 키
     *
     * @return string
     */
    public function getDefaultConfigParam(): string;

    /**
     * @return string
     */
    public function getComponentName(): string;

    /**
     * .idea 디렉토리 어딘가에 저장된 IDE 설정값을 해석해 가져옴
     */
    public function getIdeaSetup(): array;
}
