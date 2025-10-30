# Dispenser

올투그린파트너스 Dispenser 관리 시스템

## 프로젝트 구조

```
dispenser/
├── config/              # 설정 파일
│   └── schema.sql       # 데이터베이스 스키마
├── public/              # 웹 공개 디렉토리
│   ├── inc/             # 공통 인클루드 파일
│   │   ├── functions/   # 유틸리티 함수
│   │   ├── common.php   # 공통 설정
│   │   └── ini.php      # 세션 초기화
│   ├── doc/             # 페이지별 문서
│   │   ├── hq/          # 본사 페이지
│   │   ├── vendor/      # 벤더 페이지
│   │   ├── customer/    # 고객 페이지
│   │   └── lucid/       # Lucid 페이지
│   ├── dbconfig.php     # DB 연결 설정
│   ├── index.php        # 메인 진입점
│   ├── login.php        # 로그인 페이지
│   └── member.php       # 회원가입 페이지
├── src/                 # 애플리케이션 소스 코드
├── tests/               # 테스트 파일
├── vendor/              # Composer 의존성 (자동 생성)
└── composer.json        # Composer 설정
```

## 요구사항

- PHP >= 7.4
- MySQL/MariaDB
- Composer

## 설치 방법

### 1. Composer 의존성 설치

```bash
composer install
```

### 2. 데이터베이스 설정

데이터베이스 연결 정보를 수정하려면 `public/dbconfig.php` 파일을 편집하세요:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dispenser');
```

### 3. 데이터베이스 스키마 생성

```bash
mysql -u root -p < config/schema.sql
```

또는 MySQL 클라이언트에서:

```sql
SOURCE config/schema.sql;
```

### 4. 권한 설정

세션 디렉토리에 쓰기 권한 부여:

```bash
chmod -R 770 public/inc/sessions
```

## 사용 방법

### 개발 서버 실행

```bash
php -S localhost:8000 -t public
```

브라우저에서 http://localhost:8000 접속

### 기본 관리자 계정

- **이메일**: admin@dispenser.com
- **아이디**: admin
- **비밀번호**: admin123

## 주요 기능

- **역할 기반 접근 제어**: HQ(본사), VENDOR(벤더), CUSTOMER(고객)
- **회원 관리**: 사용자, 벤더, 고객 통합 관리
- **보안**: 비밀번호 암호화(bcrypt), 세션 관리
- **감사 로그**: 주요 액션 추적
- **AJAX 기반 SPA**: 페이지 리로드 없는 화면 전환

## 개발 가이드

프로젝트 운영 지침은 `public/지침.text` 파일을 참조하세요.

## 테스트

```bash
composer test
```

## 문제 해결

### 데이터베이스 연결 오류

- MySQL 서비스가 실행 중인지 확인
- dbconfig.php의 연결 정보 확인
- 데이터베이스가 생성되었는지 확인

### 세션 오류

- public/inc/sessions 디렉토리 존재 및 권한 확인
- PHP 세션 설정 확인

### TCPDF 오류

- composer install이 정상적으로 완료되었는지 확인
- vendor/autoload.php가 존재하는지 확인