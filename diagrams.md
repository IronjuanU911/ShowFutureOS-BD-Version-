# Diagramas de Arquitectura — ShowFuture OS v1

Este documento contiene la especificación visual completa del sistema **ShowFuture OS v1**, dividida en tres secciones:
1. **Diagrama Entidad-Relación de la Base de Datos (BD)**
2. **Diagrama de Clases y Modelos (UML)**
3. **Diagramas de Procesos de Negocio (Workflows)**

---

## 1. Diagrama de la Base de Datos (ER Diagram)

El siguiente diagrama representa todas las tablas, llaves primarias (PK), llaves foráneas (FK), atributos y relaciones cardinales de la base de datos relacional.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        string avatar
        string phone
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    ROLES {
        bigint id PK
        string name
        string slug
        text description
        timestamp created_at
        timestamp updated_at
    }

    ROLE_USER {
        bigint user_id FK
        bigint role_id FK
    }

    CLIENTS {
        bigint id PK
        string name
        string status
        string level
        decimal ads_budget
        bigint account_manager_id FK
        date start_date
        date renewal_date
        boolean has_sla
        string main_platform
        string main_objective
        text notes
        string logo
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    CLIENT_CONTACTS {
        bigint id PK
        bigint client_id FK
        string name
        string role
        string email
        string phone
        boolean is_primary
        timestamp created_at
        timestamp updated_at
    }

    CLIENT_ACCESSES {
        bigint id PK
        bigint client_id FK
        string platform
        string url
        string username
        string password
        text credentials
        string access_type
        string status
        text notes
        timestamp created_at
        timestamp updated_at
    }

    ONBOARDINGS {
        bigint id PK
        bigint client_id FK
        string status
        boolean communication_rules_sent
        boolean calendar_shared
        date started_at
        date target_completion_date
        integer progress_percentage
        date completed_at
        text notes
        text communication_channel
        timestamp created_at
        timestamp updated_at
    }

    ONBOARDING_CHECKLIST_ITEMS {
        bigint id PK
        bigint onboarding_id FK
        integer day_number
        string title
        text description
        boolean is_completed
        timestamp completed_at
        bigint completed_by FK
        integer order
        integer sort_order
        timestamp created_at
        timestamp updated_at
    }

    TICKETS {
        bigint id PK
        bigint client_id FK
        bigint created_by FK
        bigint assigned_to FK
        string title
        text description
        string type
        string status
        string priority
        date due_date
        decimal estimated_hours
        decimal actual_hours
        string delivery_url
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }

    TICKET_COMMENTS {
        bigint id PK
        bigint ticket_id FK
        bigint user_id FK
        text comment
        boolean is_internal
        timestamp created_at
        timestamp updated_at
    }

    TICKET_ATTACHMENTS {
        bigint id PK
        bigint ticket_id FK
        bigint user_id FK
        string file_name
        string file_path
        bigint file_size
        string mime_type
        timestamp created_at
        timestamp updated_at
    }

    TICKET_HISTORY {
        bigint id PK
        bigint ticket_id FK
        bigint user_id FK
        string field_changed
        text old_value
        text new_value
        timestamp created_at
        timestamp updated_at
    }

    QA_REVIEWS {
        bigint id PK
        bigint ticket_id FK
        bigint reviewer_id FK
        string status
        integer score
        text feedback
        text changes_requested
        timestamp approved_at
        timestamp created_at
        timestamp updated_at
    }

    %% Relaciones
    USERS ||--o{ ROLE_USER : "posee"
    ROLES ||--o{ ROLE_USER : "asignado_a"
    USERS ||--o{ CLIENTS : "gestiona (AM)"
    CLIENTS ||--o{ CLIENT_CONTACTS : "posee"
    CLIENTS ||--o{ CLIENT_ACCESSES : "proporciona"
    CLIENTS ||--|| ONBOARDINGS : "atraviesa"
    ONBOARDINGS ||--o{ ONBOARDING_CHECKLIST_ITEMS : "contiene"
    USERS ||--o{ ONBOARDING_CHECKLIST_ITEMS : "completa"
    CLIENTS ||--o{ TICKETS : "registra"
    USERS ||--o{ TICKETS : "crea"
    USERS ||--o{ TICKETS : "ejecuta"
    TICKETS ||--o{ TICKET_COMMENTS : "tiene"
    USERS ||--o{ TICKET_COMMENTS : "escribe"
    TICKETS ||--o{ TICKET_ATTACHMENTS : "contiene"
    TICKETS ||--o{ TICKET_HISTORY : "registra"
    TICKETS ||--o{ QA_REVIEWS : "recibe"
    USERS ||--o{ QA_REVIEWS : "evalúa"
```

---

## 2. Diagrama de Clases (UML Class Diagram)

Muestra los modelos de Eloquent, sus propiedades, métodos, Enums asociados y relaciones dentro del framework Laravel.

```mermaid
classDiagram
    class User {
        +BigInt id
        +String name
        +String email
        +String password
        +String phone
        +Boolean is_active
        +roles() BelongsToMany
        +managedClients() HasMany
        +assignedTickets() HasMany
        +createdTickets() HasMany
        +hasRole(slug: String) Boolean
        +isAdmin() Boolean
        +isAccountManager() Boolean
    }

    class Role {
        +BigInt id
        +String name
        +String slug
        +String description
        +users() BelongsToMany
    }

    class Client {
        +BigInt id
        +String name
        +ClientStatus status
        +ClientLevel level
        +Decimal ads_budget
        +BigInt account_manager_id
        +Date start_date
        +Date renewal_date
        +Boolean has_sla
        +String main_platform
        +String main_objective
        +String notes
        +accountManager() BelongsTo
        +contacts() HasMany
        +accesses() HasMany
        +onboarding() HasOne
        +tickets() HasMany
        +primaryContact() HasOne
    }

    class ClientContact {
        +BigInt id
        +BigInt client_id
        +String name
        +String role
        +String email
        +String phone
        +Boolean is_primary
        +client() BelongsTo
    }

    class ClientAccess {
        +BigInt id
        +BigInt client_id
        +String platform
        +String url
        +String username
        +String password
        +String notes
        +client() BelongsTo
    }

    class Onboarding {
        +BigInt id
        +BigInt client_id
        +String status
        +Date started_at
        +Date target_completion_date
        +Integer progress_percentage
        +Date completed_at
        +client() BelongsTo
        +items() HasMany
        +checklistItems() HasMany
        +getProgressPercentageAttribute() Integer
        +getIsCompleteAttribute() Boolean
    }

    class OnboardingChecklistItem {
        +BigInt id
        +BigInt onboarding_id
        +Integer day_number
        +String title
        +String description
        +Boolean is_completed
        +DateTime completed_at
        +BigInt completed_by
        +Integer order
        +onboarding() BelongsTo
        +completedByUser() BelongsTo
    }

    class Ticket {
        +BigInt id
        +BigInt client_id
        +BigInt created_by
        +BigInt assigned_to
        +String title
        +String description
        +TicketType type
        +TicketStatus status
        +TicketPriority priority
        +Date due_date
        +Decimal estimated_hours
        +Decimal actual_hours
        +String delivery_url
        +client() BelongsTo
        +createdBy() BelongsTo
        +assignedTo() BelongsTo
        +comments() HasMany
        +attachments() HasMany
        +history() HasMany
        +qaReviews() HasMany
    }

    class QaReview {
        +BigInt id
        +BigInt ticket_id
        +BigInt reviewer_id
        +String status
        +Integer score
        +String feedback
        +String changes_requested
        +DateTime approved_at
        +ticket() BelongsTo
        +reviewer() BelongsTo
    }

    class ClientStatus {
        <<enumeration>>
        Onboarding
        Active
        Paused
        Closed
    }

    class ClientLevel {
        <<enumeration>>
        Starter
        Growth
        Scale
    }

    class TicketType {
        <<enumeration>>
        Creative
        Ads
        Copy
        Commercial
    }

    class TicketStatus {
        <<enumeration>>
        Backlog
        InProgress
        Qa
        Delivered
    }

    class TicketPriority {
        <<enumeration>>
        High
        Medium
        Low
    }

    User "1" -- "*" Role : posee_roles
    User "1" -- "*" Client : account_manager
    Client "1" -- "*" ClientContact : tiene_contactos
    Client "1" -- "*" ClientAccess : tiene_accesos
    Client "1" -- "1" Onboarding : posee_onboarding
    Onboarding "1" -- "*" OnboardingChecklistItem : tiene_items
    User "1" -- "*" OnboardingChecklistItem : completa_item
    Client "1" -- "*" Ticket : genera_tickets
    User "1" -- "*" Ticket : asignado_a
    Ticket "1" -- "*" QaReview : requiere_qa
    User "1" -- "*" QaReview : revisa_qa
    Client --> ClientStatus
    Client --> ClientLevel
    Ticket --> TicketType
    Ticket --> TicketStatus
    Ticket --> TicketPriority
```

---

## 3. Diagramas de Procesos (Workflows)

### Proceso A: Onboarding Automatizado del Cliente (1–7 Días)

```mermaid
flowchart TD
    Start([Inicio: Alta de Cliente]) --> Step1[Registrar Cliente en Sistema CRM]
    Step1 --> AutoInit[Crear Registro de Onboarding & Plantilla 1-7 Días]
    AutoInit --> SetStatusOnboarding[Estado Cliente = ONBOARDING]
    
    SetStatusOnboarding --> Day1[Día 1: Reunión Kickoff & Bienvenida]
    Day1 --> Day2[Día 2: Recepción & Verificación de Accesos]
    Day2 --> Day3[Día 3: Auditoría Inicial de Activos & Métricas]
    Day3 --> Day4[Día 4: Estrategia de Pauta & Ángulos de Comunicación]
    Day4 --> Day5[Día 5: Configuración de Píxel, Eventos & CAPI]
    Day5 --> Day6[Día 6: Redacción de Copies & Producción Creativa]
    Day6 --> Day7[Día 7: Control QA Final & Lanzamiento de Campañas]
    
    Day7 --> CheckProgress{¿Todas las tareas completadas?}
    CheckProgress -- No --> WaitTask[Esperar completitud de ítems]
    WaitTask --> CheckProgress
    
    CheckProgress -- Sí (100%) --> CompleteOnboarding[Marcar Onboarding como COMPLETED]
    CompleteOnboarding --> AutoStatusActive[Transición Automática: Estado Cliente = ACTIVE]
    AutoStatusActive --> EndOnboarding([Fin del Onboarding])
```

---

### Proceso B: Ciclo de Vida de Tickets y Control QA (Kanban)

```mermaid
flowchart TD
    StartTicket([Inicio: Solicitud de Trabajo]) --> CreateTicket[Crear Ticket en Kanban]
    CreateTicket --> SetBacklog[Estado = BACKLOG]
    SetBacklog --> Assign[Asignar Especialista: Ads / Copy / Creativo]
    
    Assign --> MoveInProgress[Especialista toma ticket: Estado = IN_PROGRESS]
    MoveInProgress --> Work[Ejecución del Entregable]
    Work --> UploadDelivery[Adjuntar Entregable o URL de Producción]
    UploadDelivery --> MoveQA[Enviar a Revisión: Estado = QA]
    
    MoveQA --> QAReview[Account Manager / Lead revisa Entregables]
    QAReview --> Decision{¿Cumple Criterios de Aceptación?}
    
    Decision -- No (Rechazado) --> RequestChanges[Registrar Feedback y Cambios Solicitados]
    RequestChanges --> RevertProgress[Estado = IN_PROGRESS]
    RevertProgress --> Work
    
    Decision -- Sí (Aprobado) --> ApproveQA[Registrar Calificación & Aprobación QA]
    ApproveQA --> SetDelivered[Estado = DELIVERED]
    SetDelivered --> NotifyClient[Notificar al Cliente / AM]
    NotifyClient --> EndTicket([Fin del Ticket])
```
