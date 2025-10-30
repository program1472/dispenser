# Dispenser

PHP 프로젝트

## 프로젝트 구조

```
dispenser/
├── src/           # 소스 코드
├── public/        # 공개 파일 (index.php 등)
├── config/        # 설정 파일
├── tests/         # 테스트 파일
├── vendor/        # Composer 의존성 (자동 생성)
└── composer.json  # Composer 설정
```

## 설치

```bash
composer install
```

## 사용 방법

개발 서버 실행:

```bash
php -S localhost:8000 -t public
```

## 테스트

```bash
composer test
```