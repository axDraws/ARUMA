# Diagramas UML - Proyecto ARUMA Spa

## 1. Diagrama de Clases (Modelo de Datos)

```mermaid
classDiagram
    class User {
        -int id
        -string role
        -string nombre
        -string email
        -string telefono
        -string password_hash
        -date fecha_nac
        -string direccion
        -datetime created_at
    }

    class Client {
        +getAllClients()
        +find(id)
        +findByEmail(email)
        +create(nombre, email, password_hash)
        +update(id, data)
        +delete(id)
    }

    class Therapist {
        -int id
        -string nombre
        -string especialidad
        -string telefono
        -bool activo
        -datetime created_at
        +getAllTherapists()
        +getTherapistById(id)
        +create(data)
        +update(id, data)
        +delete(id)
    }

    class Service {
        -int id
        -string nombre
        -int duracion_min
        -decimal precio
        -string categoria
        -bool activo
        -string descripcion
        -datetime created_at
        +getAllServices()
        +find(id)
        +getServiceById(id)
        +create(nombre, duracion, precio, categoria, descripcion)
        +update(id, data)
        +delete(id)
    }

    class Reservation {
        -int id
        -int cliente_id
        -int servicio_id
        -int therapist_id
        -date fecha
        -time hora
        -int duracion_min
        -string estado
        -string notas
        -datetime created_at
        +all()
        +create(data)
        +find(id)
        +update(id, data)
        +delete(id)
        +getByCliente(cliente_id)
        +getByTherapist(therapist_id)
    }

    class Product {
        -int id
        -string nombre
        -string descripcion
        -decimal precio
        -string imagen_path
        -datetime created_at
        +getAllProducts()
        +find(id)
        +create(data)
        +update(id, data)
        +delete(id)
    }

    class History {
        -int id
        -int reservation_id
        -int user_id
        -string evento
        -string detalle
        -datetime created_at
        +create(data)
        +getByReservation(reservation_id)
        +getByUser(user_id)
        +getAll()
    }

    class DB {
        -string host
        -string db
        -string user
        -string pass
        -string charset
        +get() PDO
    }

    %% Relaciones
    Reservation --> User : cliente_id
    Reservation --> Service : servicio_id
    Reservation --> Therapist : therapist_id
    History --> Reservation : reservation_id
    History --> User : user_id
    Client --> User : extends
```

## 2. Diagrama de Arquitectura MVC

```mermaid
graph TB
    subgraph Client["Capa de Presentación (Views)"]
        Home["home.php"]
        Cliente["cliente.php"]
        Admin["administrador.php"]
        Perfil["mi_perfil.php"]
    end

    subgraph Controller["Capa de Controladores"]
        AC["AuthController"]
        CC["ClientController"]
        RC["ReservationController"]
        SC["ServiceController"]
        TC["TherapistController"]
        PC["ProductController"]
        HC["HistoryController"]
        AdmC["AdminController"]
    end

    subgraph Model["Capa de Modelos (Lógica de Negocio)"]
        UM["UserModel"]
        CM["ClientModel"]
        RM["ReservationModel"]
        SM["ServiceModel"]
        TM["TherapistModel"]
        PM["ProductModel"]
        HM["HistoryModel"]
    end

    subgraph Database["Capa de Datos"]
        DB["Base de Datos<br/>aruma_spa"]
    end

    subgraph Public["Recursos Públicos"]
        CSS["CSS<br/>bootstrap.min.css<br/>style.css"]
        JS["JavaScript<br/>script.js<br/>bootstrap.bundle.min.js"]
        IMG["Imágenes"]
    end

    %% Flujos de solicitud
    Home --> AC
    Cliente --> CC
    Admin --> AdmC
    Perfil --> CC

    AC --> UM
    CC --> CM
    RC --> RM
    SC --> SM
    TC --> TM
    PC --> PM
    HC --> HM
    AdmC --> CM
    AdmC --> SM
    AdmC --> TM
    AdmC --> PM

    UM --> DB
    CM --> DB
    RM --> DB
    SM --> DB
    TM --> DB
    PM --> DB
    HM --> DB

    Home --> CSS
    Home --> JS
    Home --> IMG
```

## 3. Diagrama de Casos de Uso

```mermaid
graph TB
    subgraph Actor["Actores"]
        U["Usuario No Autenticado"]
        C["Cliente Autenticado"]
        A["Administrador"]
    end

    subgraph UseCase["Casos de Uso"]
        Login["Iniciar Sesión"]
        Register["Registrarse"]
        ViewServices["Ver Servicios"]
        ViewTherapists["Ver Terapeutas"]
        MakeReservation["Hacer Reserva"]
        ViewReservations["Ver Mis Reservas"]
        UpdateProfile["Actualizar Perfil"]
        ViewHistory["Ver Historial"]
        
        ManageServices["Gestionar Servicios"]
        ManageTherapists["Gestionar Terapeutas"]
        ManageProducts["Gestionar Productos"]
        ManageClients["Gestionar Clientes"]
        ViewAnalytics["Ver Reportes"]
    end

    %% Relaciones Usuario No Autenticado
    U --> Login
    U --> Register
    U --> ViewServices
    U --> ViewTherapists

    %% Relaciones Cliente
    C --> UpdateProfile
    C --> MakeReservation
    C --> ViewReservations
    C --> ViewHistory
    C --> ViewServices
    C --> ViewTherapists

    %% Relaciones Administrador
    A --> ManageServices
    A --> ManageTherapists
    A --> ManageProducts
    A --> ManageClients
    A --> ViewAnalytics

    %% Inclusiones
    MakeReservation -.-> ViewServices
    MakeReservation -.-> ViewTherapists
    ViewReservations -.-> ViewHistory
```

## 4. Diagrama de Secuencia - Flujo de Registro

```mermaid
sequenceDiagram
    participant U as Usuario
    participant View as home.php
    participant Controller as AuthController
    participant Model as UserModel
    participant DB as Database

    U ->> View: Completa formulario de registro
    View ->> Controller: POST /registro
    Controller ->> Controller: Validar datos (email, nombre)
    Controller ->> Model: findByEmail(email)
    Model ->> DB: SELECT * FROM users WHERE email
    DB ->> Model: Verificar existencia
    Model ->> Controller: Email no existe
    Controller ->> Model: create(nombre, email, password_hash)
    Model ->> DB: INSERT INTO users
    DB ->> Model: lastInsertId()
    Model ->> Controller: ID del nuevo usuario
    Controller ->> View: Redirect a /login
    View ->> U: Mostrar página de login
```

## 5. Diagrama de Secuencia - Flujo de Reserva

```mermaid
sequenceDiagram
    participant C as Cliente
    participant View as cliente.php
    participant Controller as ReservationController
    participant RM as ReservationModel
    participant HM as HistoryModel
    participant DB as Database

    C ->> View: Selecciona servicio y fecha
    View ->> Controller: POST /reservar
    Controller ->> Controller: Validar datos de reserva
    Controller ->> RM: create(cliente_id, servicio_id, therapist_id, fecha, hora)
    RM ->> DB: INSERT INTO reservations
    DB ->> RM: ID de reserva creada
    RM ->> HM: create(reservation_id, "reserva_creada")
    HM ->> DB: INSERT INTO history
    DB ->> HM: Historial registrado
    RM ->> Controller: Reserva exitosa
    Controller ->> View: Redirect a /cliente
    View ->> C: Mostrar confirmación
```

## 6. Diagrama de Secuencia - Flujo de Login

```mermaid
sequenceDiagram
    participant U as Usuario
    participant View as home.php
    participant Controller as AuthController
    participant Model as UserModel
    participant DB as Database

    U ->> View: Ingresa email y contraseña
    View ->> Controller: POST /login
    Controller ->> Model: findByEmail(email)
    Model ->> DB: SELECT * FROM users WHERE email
    DB ->> Model: Retorna usuario
    Model ->> Controller: Usuario encontrado
    Controller ->> Controller: password_verify(password, hash)
    alt Contraseña válida
        Controller ->> Controller: Crear sesión
        Controller ->> View: Redirect según rol
        View ->> U: Dashboard (Cliente o Admin)
    else Contraseña inválida
        Controller ->> View: Mostrar error
        View ->> U: Alert y redirect a home
    end
```

## 7. Diagrama de Base de Datos (ER)

```mermaid
erDiagram
    USERS ||--o{ RESERVATIONS : makes
    USERS ||--o{ HISTORY : has
    SERVICES ||--o{ RESERVATIONS : includes
    THERAPISTS ||--o{ RESERVATIONS : assigned
    RESERVATIONS ||--o{ HISTORY : recorded

    USERS {
        int id PK
        enum role
        string nombre
        string email UK
        string telefono
        string password_hash
        date fecha_nac
        string direccion
        timestamp created_at
    }

    THERAPISTS {
        int id PK
        string nombre
        string especialidad
        string telefono
        boolean activo
        timestamp created_at
    }

    SERVICES {
        int id PK
        string nombre
        int duracion_min
        decimal precio
        string categoria
        boolean activo
        text descripcion
        timestamp created_at
    }

    PRODUCTS {
        int id PK
        string nombre
        text descripcion
        decimal precio
        string imagen_path
        timestamp created_at
    }

    RESERVATIONS {
        int id PK
        int cliente_id FK
        int servicio_id FK
        int therapist_id FK
        date fecha
        time hora
        int duracion_min
        enum estado
        text notas
        timestamp created_at
    }

    HISTORY {
        int id PK
        int reservation_id FK
        int user_id FK
        string evento
        text detalle
        timestamp created_at
    }
```

## 8. Diagrama de Estados - Reserva

```mermaid
stateDiagram-v2
    [*] --> Pendiente
    
    Pendiente --> Confirmada: Cliente/Admin confirma
    Pendiente --> Cancelada: Cliente/Admin cancela
    
    Confirmada --> En_Proceso: Inicia servicio
    Confirmada --> Cancelada: Se cancela
    
    En_Proceso --> Completada: Servicio finalizado
    En_Proceso --> Cancelada: Se cancela durante
    
    Completada --> [*]
    Cancelada --> [*]
```

## 9. Matriz de Relaciones entre Componentes

| Componente | UserModel | ClientModel | ReservationModel | ServiceModel | TherapistModel |
|---|---|---|---|---|---|
| AuthController | ✓ | - | - | - | - |
| ClientController | ✓ | ✓ | ✓ | ✓ | - |
| ReservationController | - | - | ✓ | ✓ | ✓ |
| ServiceController | - | - | - | ✓ | - |
| TherapistController | - | - | - | - | ✓ |
| AdminController | ✓ | ✓ | ✓ | ✓ | ✓ |
| HistoryController | - | - | ✓ | - | - |

## 10. Estructura de Carpetas y Dependencias

```mermaid
graph TB
    Root["ARUMA/"]
    
    Root --> Config["app/config.php<br/>(Conexión DB)"]
    Root --> Views["views/"]
    Root --> Controllers["Controller/"]
    Root --> Models["Model/"]
    Root --> Public["public/"]
    Root --> Router["router.php"]
    Root --> Index["index.php"]

    Views --> V1["home.php"]
    Views --> V2["cliente.php"]
    Views --> V3["administrador.php"]
    Views --> V4["mi_perfil.php"]

    Controllers --> C1["AuthController.php"]
    Controllers --> C2["ClientController.php"]
    Controllers --> C3["ReservationController.php"]
    Controllers --> C4["ServiceController.php"]
    Controllers --> C5["TherapistController.php"]
    Controllers --> C6["ProductController.php"]
    Controllers --> C7["HistoryController.php"]
    Controllers --> C8["AdminController.php"]

    Models --> M1["UserModel.php"]
    Models --> M2["ClientModel.php"]
    Models --> M3["ReservationModel.php"]
    Models --> M4["ServiceModel.php"]
    Models --> M5["TherapistModel.php"]
    Models --> M6["ProductModel.php"]
    Models --> M7["HistoryModel.php"]

    Public --> CSS["estilos/"]
    Public --> JS["scripts/"]
    Public --> IMG["img/"]

    Index --> Router
    Router --> Controllers
    Controllers --> Models
    Models --> Config
    V1 --> JS
    V2 --> JS
    V3 --> JS
    V4 --> JS
```

---

## Explicación de la Arquitectura

### Patrones Utilizados:
- **MVC (Model-View-Controller)**: Separación clara entre lógica, datos y presentación
- **DAO (Data Access Object)**: Modelos actúan como DAO para acceso a datos
- **Singleton Pattern**: Clase DB proporciona única conexión

### Flujo de Solicitud Típico:
1. Usuario interactúa con **View** (HTML/PHP)
2. **Router** dirige a **Controller** apropiado
3. **Controller** procesa lógica y llama a **Models**
4. **Models** ejecutan consultas usando **DB**
5. Resultado retorna a **Controller**
6. **Controller** carga **View** con datos

### Tablas Principales:
- **users**: Almacena clientes y administradores
- **therapists**: Registro de terapeutas
- **services**: Servicios disponibles
- **reservations**: Citas/reservas con estados
- **history**: Auditoría de cambios
- **products**: Productos del spa

### Relaciones:
- Un **Cliente** puede hacer muchas **Reservas**
- Una **Reserva** requiere un **Servicio**
- Una **Reserva** puede asignarse a un **Terapeuta**
- Cada **Reserva** genera **Historial**
